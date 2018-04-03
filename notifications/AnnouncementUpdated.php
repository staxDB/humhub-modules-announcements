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
class AnnouncementUpdated extends BaseNotification
{

    /**
     * @since 1.2.3
     * @see NotificationManager
     * @var boolean do not send this notification also to the originator
     */
    public $suppressSendToOriginator = true;

    /**
     * @inheritdoc
     */
    public $moduleId = 'announcements';

    /**
     * @inheritdoc
     */
    public $viewName = 'announcementNotification';

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
    public function html()
    {
        return Yii::t('AnnouncementsModule.notifications', '{displayName} updated an Announcement in space {spaceName}.', [
            'displayName' => Html::tag('strong', Html::encode($this->originator->displayName)),
            'spaceName' =>  Html::tag('strong',Html::encode($this->source->content->container->displayName))
        ]);
    }

    /**
     *  @inheritdoc
     */
    public function getMailSubject()
    {
        return Yii::t('AnnouncementsModule.notifications', '{displayName} updated an Announcement.', [
            'displayName' => Html::encode($this->originator->displayName),
        ]);
    }

}
