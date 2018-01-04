<?php

namespace humhub\modules\announcements\notifications;

use Yii;
use yii\bootstrap\Html;
use humhub\modules\notification\components\BaseNotification;

/**
 * SpaceAnnouncementCreatedNotification is sent to alls members of the space
 *
 * @author davidborn
 */
class AnnouncementCreated extends BaseNotification
{

    /**
     * @inheritdoc
     */
    public $moduleId = "announcements";

    /**
     * @inheritdoc
     */
//    public $viewName = "announcementCreated";

    /**
     *  @inheritdoc
     */
    public function category()
    {
        return new AnnouncementNotificationCategory();
    }

    /**
     *  @inheritdoc
     */
    public function getMailSubject()
    {
        return strip_tags($this->html());
    }

    public function html() {
        return Yii::t('AnnouncementsModule.base', '{userName} created a new Announcement.', [
            '{userName}' => '<strong>' . Html::encode($this->originator->displayName) . '</strong>',
        ]);
    }

}