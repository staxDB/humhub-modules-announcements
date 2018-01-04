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
     * @inheritdoc
     */
    public $moduleId = "announcements";


    public function html() {
        return Yii::t('AnnouncementsModule.base', '{userName} updated an Announcement.', [
            '{userName}' => '<strong>' . Html::encode($this->originator->displayName) . '</strong>',
        ]);
    }

}