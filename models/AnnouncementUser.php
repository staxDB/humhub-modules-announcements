<?php

namespace humhub\modules\announcements\models;

use humhub\components\ActiveRecord;
use humhub\modules\user\models\User;
use Yii;

/**
 * This is the model class for table "announcement_user".
 *
 * The followings are the available columns in table 'announcement_user':
 * @property integer $id
 * @property integer $announcement_id
 * @property integer $user_id
 * @property integer $confirmed
 * @property string $updated_at
 * @property string $created_at
 *
 * @author David Born ([staxDB](https://github.com/staxDB))
 */
class AnnouncementUser extends ActiveRecord
{

    /**
     * @return string the associated database table name
     */
    public static function tableName()
    {
        return 'announcement_user';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return [
            [['announcement_id', 'confirmed'], 'required'],
            [['announcement_id'], 'integer'],
            [['confirmed'], 'boolean'],
        ];
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return [
            'confirmed' => Yii::t('AnnouncementsModule.models', 'Read'),
        ];
    }

    public function followContent($follow = true)
    {
        // user should follow the content...
        $obj = $this->announcement->content->getPolymorphicRelation();
        $obj->follow($this->user->id, $follow);
    }

    public function getAnnouncement()
    {
        return $this->hasOne(Announcement::class, ['id' => 'announcement_id']);
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function afterSave($insert, $changedAttributes)
    {
        $this->followContent();
        parent::afterSave($insert, $changedAttributes); // TODO: Change the autogenerated stub
    }

    public function afterDelete()
    {
        $this->followContent(false);
        parent::afterDelete(); // TODO: Change the autogenerated stub
    }

}
