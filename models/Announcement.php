<?php

namespace humhub\modules\announcements\models;

use humhub\modules\user\models\User;
use Yii;
use humhub\modules\content\components\ContentActiveRecord;
use humhub\modules\announcements\models\AnnouncementUser;

/**
 * This is the model class for table "announcements".
 *
 * The followings are the available columns in table 'announcements':
 *
 * @property integer $id
 * @property string $title
 * @property string $message
 * @property string $created_at
 * @property string $updated_at
 *
 * @author davidborn
 */
class Announcement extends ContentActiveRecord implements \humhub\modules\search\interfaces\Searchable
{
    const SCENARIO_CREATE = 'create';
    const SCENARIO_EDIT = 'edit';
    const SCENARIO_CLOSE = 'close';

    public $autoAddToWall = true;
    public $wallEntryClass = 'humhub\modules\announcements\widgets\WallEntry';

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
            self::SCENARIO_CREATE => ['title', 'message'],
            self::SCENARIO_EDIT => ['title', 'message']
        ];
    }


    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
//            [['title', 'message'], 'required'],
            [['title', 'message'], 'string'],
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'title' => Yii::t('AnnouncementsModule.base', 'Title'),
            'message' => Yii::t('AnnouncementsModule.base', 'Message'),
        );
    }

    public function getConfirmations()
    {
        return $this->hasMany(AnnouncementUser::className(), ['announcement_id' => 'id']);
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
        $recipient = $this->findAnnouncementUser($user);

        if (!$recipient) {
            $recipient = new AnnouncementUser();
        }

        $recipient->user_id = $user->id;
        $recipient->announcement_id = $this->id;
        $recipient->confirmed = $state;
        $recipient->save();
    }

    /**
     * Finds a AnnouncementUser instance for the given user or the logged in user if no user provided.
     *
     * @param User $user
     * @return SpaceNewsRecipient
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
     * @return SpaceNewsRecipient
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

        if(!$currentUser) {
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
        if ($total == 0)
            return 0;

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
        return $this->hasUserConfirmed();
    }


    /**
     * Resets all answers from a user only if the poll is not closed yet.
     *
     * @param type $userId
     */
    public function resetConfirmation($userId = "")
    {

        if ($userId == "")
            $userId = Yii::$app->user->id;

        if ($this->hasUserConfirmed($userId)) {

            $userConfirmation = $this->getConfirmations()->where(['user_id' => $userId])->one();
            $userConfirmation->confirmed = false;
            $userConfirmation->save();
        }
    }

    /**
     * @param type $insert
     * @param type $changedAttributes
     * @return boolean
     */
    public function afterSave($insert, $changedAttributes)
    {
        if ($insert || $changedAttributes) {
            $members = $this->content->container->getMembershipUser()->all();
            foreach ($members as $member) {
                $this->setConfirmation($member);
            }
        }

        parent::afterSave($insert, $changedAttributes);

        return true;
    }
    
    public function beforeSave($insert)
    {
        return parent::beforeSave($insert); // TODO: Change the autogenerated stub
    }


    /**
     * Deletes a Announcement including its dependencies.
     */
    public function beforeDelete()
    {
//        foreach ($this->confirmations as $answer) {
//            $answer->delete();
//        }
        return parent::beforeDelete();
    }

    /**
     * Checks if user has confirmed
     *
     * @param type $userId
     * @return type
     */
    public function hasUserConfirmed($userId = "")
    {
        $confirmedUser = $this->findAnnouncementUserById($userId);

        if ($confirmedUser == null)
            return false;
        if ($confirmedUser->confirmed == false || $confirmedUser->confirmed == null)
            return false;

        return true;
    }

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

        if ($confirmed) {
            $activity = new \humhub\modules\announcements\activities\NewConfirm();
            $activity->source = $this;
            $activity->originator = Yii::$app->user->getIdentity();
            $activity->create();
        }
    }

    /**
     * @inheritdoc
     */
    public function getContentName()
    {
        return Yii::t('AnnouncementsModule.base', "Message");
    }

    /**
     * @inheritdoc
     */
    public function getContentDescription()
    {
        return $this->title;
    }

    /**
     * @inheritdoc
     */
    public function getSearchAttributes()
    {

        return array(
            'title' => $this->title,
            'message' => $this->message
        );
    }

}
