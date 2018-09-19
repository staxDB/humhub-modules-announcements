<?php

namespace humhub\modules\announcements\models;

use humhub\modules\announcements\models\forms\EditForm;
use humhub\modules\announcements\permissions\MoveContent;
use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\notification\models\Notification;
use humhub\modules\space\models\Membership;
use humhub\modules\space\models\Space;
use humhub\modules\user\models\User;
use humhub\modules\content\components\ContentActiveRecord;
use humhub\modules\announcements\permissions\CreateAnnouncement;
use humhub\modules\announcements\permissions\ViewStatistics;
use humhub\modules\announcements\notifications\AnnouncementCreated;
use humhub\modules\announcements\notifications\AnnouncementUpdated;
use humhub\modules\search\interfaces\Searchable;
use humhub\widgets\Label;
use Yii;

/**
 * This is the model class for table "announcements".
 *
 * The followings are the available columns in table 'announcements':
 *
 * @property integer $id
 * @property string $message
 * @property int $closed
 * @property string $created_at
 * @property string $updated_at
 *
 * @author David Born ([staxDB](https://github.com/staxDB))
 */
class Announcement extends ContentActiveRecord implements Searchable
{
    const SCENARIO_CREATE = 'create';
    const SCENARIO_RESET = 'reset';
    const SCENARIO_EDIT = 'edit';
    const SCENARIO_CLOSE = 'close';
    const SCENARIO_DEFAULT = 'default'; // on file-upload


    /**
     * @inheritdoc
     */
    public $canMove = true;

    public $autoAddToWall = true;
    public $wallEntryClass = 'humhub\modules\announcements\widgets\WallEntry';

    // won't create any activities
    // public $silentContentCreation = true;

    /**
     * @inheritdoc
     */
    public $managePermission = CreateAnnouncement::class;

    public $moduleId = 'announcements';

    /**
     * @return string the associated database table name
     */
    public static function tableName()
    {
        return 'announcement';
    }

    public function scenarios()
    {
        return [
            self::SCENARIO_CLOSE => [],
            self::SCENARIO_RESET => [],
            self::SCENARIO_CREATE => ['message'],
            self::SCENARIO_EDIT => ['message'],
            self::SCENARIO_DEFAULT => []
        ];
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return [
            [['message'], 'required'],
            [['message'], 'string'],
        ];
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return [
            'message' => Yii::t('AnnouncementsModule.models', 'Message'),
        ];
    }

    public function hasConfirmations()
    {
        return !empty($this->confirmations);
    }

    public function getConfirmations()
    {
        return $this->hasMany(AnnouncementUser::class, ['announcement_id' => 'id']);
    }

    /**
     * Returns an ActiveQuery for all announcement_user user models of this message.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getConfirmationUsers()
    {
        return $this->hasMany(User::class, ['id' => 'user_id'])->via('confirmations');
    }

    /**
     * @param $user
     * @param boolean $state
     */
    public function setConfirmation($user, $state = false)
    {
        $announcementUser = $this->findAnnouncementUser($user);

        if (!$announcementUser) {
            $announcementUser = new AnnouncementUser();
        }

        $announcementUser->user_id = $user->id;
        $announcementUser->announcement_id = $this->id;
        $announcementUser->confirmed = $state;
        $announcementUser->save();
    }

    /**
     * Sets all announcement user entries, but keep read statistics.
     */
    public function setConfirmations()
    {
        $members = $this->getMembersOfSpace();
        $confirmed = $this->getConfirmations()->all(); // gets all confirmationUsers

        foreach ($members as $memberKey => $member) {
            foreach ($confirmed as $userKey => $user) {
                if ($member->id === $user->user->id) {
//                    $this->setConfirmation($member, $user->confirmed);
                    unset($confirmed[$userKey]); // user exists in space and in AnnouncementUser
                    unset($members[$memberKey]);
                }
            }
        }

        // add new members to list of AnnouncementUser
        foreach ($members as $memberKey => $member) {
            $this->setConfirmation($member); // user is a member but not in list of AnnouncementUser --> add to list
            unset($members[$memberKey]);
        }

        // remove old AnnouncementUser from list
        foreach ($confirmed as $userKey => $user) {
            $announcementUser = $this->findAnnouncementUser($user->user);
            $this->unlink('confirmations', $announcementUser, true);
            unset($confirmed[$userKey]);
        }
    }

    /**
     * returns all members of space except the creator (depending on settings).
     */
    private function getMembersOfSpace()
    {
        $members = $this->content->container->getMembershipUser()->all(); // gets all users in space
        $settings = EditForm::instantiate();
        if ($settings->skipCreator)
        {
            foreach ($members as $memberKey => $member) {
                if ($member->id === $this->content->createdBy->id) {
                    unset($members[$memberKey]);
                }
            }
        }
        return $members;
    }

    /**
     * Finds a AnnouncementUser instance for the given user or the logged in user if no user provided.
     *
     * @param null $id
     * @return AnnouncementUser|null|void
     */
    public function findAnnouncementUserById($id = null)
    {
        if ($id == null) {
            $currentUser = Yii::$app->user;
            if ($currentUser->isGuest) {
                return;
            }
            $id = $currentUser->id;
        }

        return AnnouncementUser::findOne(['user_id' => $id, 'announcement_id' => $this->id]);
    }

    /**
     * Finds a AnnouncementUser instance for the given user or the logged in user if no user provided.
     *
     * @param User $user
     * @return AnnouncementUser|null|void
     */
    public function findAnnouncementUser(User $user = null)
    {
        $currentUser = $user;

        if (!$currentUser) {
            $currentUser = Yii::$app->user;
            if ($currentUser->isGuest) {
                return;
            }
        }

        if (!$currentUser) {
            return;
        }

        return AnnouncementUser::findOne(['user_id' => $currentUser->id, 'announcement_id' => $this->id]);
    }

    /**
     * Returns the percentage of users confirmed this message
     *
     * @return int
     */
    public function getPercent()
    {
        $total = AnnouncementUser::find()->where(['announcement_id' => $this->id])->count();
        if ($total == 0) {
            return 0;
        }

        return $this->getConfirmedCount() / $total * 100;
    }

    /**
     * Returns the total number of confirmed users got this message
     *
     * @return int
     */
    public function getConfirmedCount()
    {
        return $this->getConfirmations()->where(['announcement_user.confirmed' => true])->count();
    }

    /**
     * Returns the total number of confirmed users got this message
     *
     * @return int
     */
    public function getUnConfirmedCount()
    {
        return $this->getConfirmations()->where(['announcement_user.confirmed' => false])->count();
    }

    /**
     * Returns the total number of confirmed users got this message
     *
     * @return int
     */
    public function getConfirmedUsers()
    {
        return $this->getConfirmations()->where(['announcement_user.confirmed' => true])->all();
    }

    /**
     * Returns the total number of confirmed users got this message
     *
     * @return int
     */
    public function getUnConfirmedUsers()
    {
        return $this->getConfirmations()->where(['announcement_user.confirmed' => false])->all();
    }

    public function isResetAllowed()
    {
        return $this->hasUserConfirmed() && !$this->closed;
    }

    public function isResetStatisticsAllowed()
    {
        return $this->content->canEdit() && !$this->closed;
    }

    /**
     * Resets all answers from a user only if the poll is not closed yet.
     *
     * @param string $userId
     */
    public function resetConfirmation($userId = '')
    {
        if ($this->closed) {
            return;
        }

        if ($userId == '') {
            $userId = Yii::$app->user->id;
        }

        if ($this->hasUserConfirmed($userId)) {
            $userConfirmation = $this->getConfirmations()->where(['user_id' => $userId])->one();
            $userConfirmation->confirmed = false;
            $userConfirmation->save();
        }
    }

    /**
     * Resets all confirmed-entries for each user of this announcement.
     */
    public function resetStatistics ()
    {
        if ($this->closed) {
            return;
        }

        if ($this->hasConfirmations()) {
            $confirmations = $this->getConfirmations()->where(['confirmed' => 1])->all();
            foreach ($confirmations as $confirmation) {
                $confirmation->updateAttributes(['confirmed' => false]);
            }
        }
    }

    /**
     * @param type $insert
     * @param type $changedAttributes
     * @return boolean
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        $this->setConfirmations();

        $settings = EditForm::instantiate();

        if ($this->scenario === self::SCENARIO_CREATE && $settings->notifyCreated && $insert) {
            $this->informUsers(true);
        } elseif ($this->scenario === self::SCENARIO_EDIT && $settings->notifyUpdated) {
            $this->informUsers(false);
        } elseif ($this->scenario === self::SCENARIO_CLOSE && $settings->notifyClosed) {
            $this->informUsers(false);
        } elseif ($this->scenario === self::SCENARIO_RESET && $settings->notifyResetStatistics) {
            $this->informUsers(false);
        }

        return true;
    }

    public function afterMove(ContentContainerActiveRecord $container = null)
    {
        $settings = EditForm::instantiate();

        if ($settings->setClosed) {
            $this->closed = true;
            $this->save();
        }
        parent::afterMove($container);
    }

    /**
     * @param $insert
     * @return bool
     * @throws \yii\base\Exception
     */
    public function beforeSave($insert)
    {
        return parent::beforeSave($insert); // TODO: Change the autogenerated stub
    }

    /**
     * Deletes a Announcement including its dependencies.
     */
    public function beforeDelete()
    {
        foreach ($this->confirmations as $confirmation) {
            $confirmation->delete();
        }

        return parent::beforeDelete();
    }

    /**
     * Checks if user has confirmed
     *
     * @param type $userId
     * @return type
     */
    public function hasUserConfirmed($userId = '')
    {
        $confirmedUser = $this->findAnnouncementUserById($userId);

        if ($confirmedUser == null) {
            return false;
        }
        
        if ($confirmedUser->confirmed == false || $confirmedUser->confirmed == null) {
            return false;
        }

        return true;
    }

    /**
     * handle click on confirm as read
     *
     * @return void
     * @throws \yii\base\Exception
     */
    public function confirm()
    {
        if ($this->hasUserConfirmed()) {
            return;
        }

        $confirmed = false;

        //TODO: write confirm-Function for current user!!!
        $confirmMessageUser = $this->findAnnouncementUser();

        if ($confirmMessageUser) {
            $confirmMessageUser->confirmed = true;

            if ($confirmMessageUser->save()) {
                $confirmed = true;
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function getContentName()
    {
        return Yii::t('AnnouncementsModule.base', 'Announcement');
    }

    /**
     * @inheritdoc
     */
    public function getContentDescription()
    {
        return $this->message;
    }

    public function getIcon()
    {
        return 'fa-bullhorn';
    }

    /**
     * @inheritdoc
     */
    public function getSearchAttributes()
    {
        return [
            'message' => $this->message
        ];
    }

    public function canShowStatistics()
    {
        return $this->content->container->permissionManager->can(ViewStatistics::class);
    }

    public function canMove(ContentContainerActiveRecord $container = null)
    {
        if(!$this->content->container->permissionManager->can(MoveContent::class))
            return Yii::t('AnnouncementsModule.permissions', 'You have insufficient permissions to move announcements!');
        else
            return parent::canMove($container);
    }

    /**
     * Invite user to this meeting
     */
    public function informUsers($newRecord = false)
    {
        // delete old notifications for this announcement
        $notifications = Notification::find()->where(['source_class' => self::class, 'source_pk' => $this->id, 'space_id' => $this->content->container->id])->all();
        foreach ($notifications as $notification) {
            $notification->delete();
        }

        if ($newRecord) {
            AnnouncementCreated::instance()->from(Yii::$app->user->getIdentity())->about($this)->sendBulk($this->confirmationUsers);
        } else {
            AnnouncementUpdated::instance()->from(Yii::$app->user->getIdentity())->about($this)->sendBulk($this->confirmationUsers);
        }
    }

    /**
     * @inheritdoc
     */
    public function getLabels($labels = [], $includeContentName = true)
    {
        if ($this->closed) {
            $labels[] = Label::danger(Yii::t('AnnouncementsModule.widgets', 'Old'))->icon('fa fa-warning')->sortOrder(350);
        }

        return parent::getLabels($labels, $includeContentName); // TODO: Change the autogenerated stub
    }
}
