<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2017 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\announcements\notifications;

use Yii;
use humhub\modules\notification\components\NotificationCategory;
use humhub\modules\notification\targets\BaseTarget;
use humhub\modules\notification\targets\MailTarget;
use humhub\modules\notification\targets\WebTarget;
use humhub\modules\notification\targets\MobileTarget;

/**
 * SpaceMemberNotificationCategory
 *
 * @author David Born ([staxDB](https://github.com/staxDB))
 */
class AnnouncementNotificationCategory extends NotificationCategory
{

    /**
     * @inheritdoc
     */
    public $id = 'announcements';

    /**
     * @inheritdoc
     */
    public function getTitle()
    {
        return Yii::t('AnnouncementsModule.base', 'Announcements');
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return Yii::t('AnnouncementsModule.notifications', 'Receive Notifications for Announcements (Create, Update, Reopen).');
    }

    /**
     * @inheritdoc
     */
    public function getDefaultSetting(BaseTarget $target)
    {
        if ($target->id === MailTarget::getId()) {
            return true;
        } elseif ($target->id === WebTarget::getId()) {
            return true;
        } elseif ($target->id === MobileTarget::getId()) {
            return true;
        }

        return $target->defaultSetting;
    }

}
