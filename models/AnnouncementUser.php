<?php

namespace humhub\modules\announcements\models;

use humhub\components\ActiveRecord;
use humhub\modules\announcements\models\Announcement;
use humhub\modules\user\models\User;

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
 * @author davidborn
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
        return array(
            array(['announcement_id', 'confirmed'], 'required'),
            [['announcement_id'], 'integer'],
            [['confirmed'], 'boolean'],
        );
    }

    public function getMessage()
    {
        return $this->hasOne(Announcement::className(), ['id' => 'announcement_id']);
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

}
