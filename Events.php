<?php

namespace humhub\modules\announcements;

use humhub\modules\announcements\models\Announcement;
use humhub\modules\announcements\models\AnnouncementUser;
use humhub\modules\announcements\models\filters\AnnouncementStreamFilter;
use humhub\modules\announcements\models\forms\EditForm;
use humhub\modules\announcements\widgets\CloseButton;
use humhub\modules\announcements\widgets\ExportButton;
use humhub\modules\announcements\widgets\ResetButton;
use humhub\modules\announcements\widgets\ResetStatisticsButton;
use humhub\modules\content\helpers\ContentContainerHelper;
use humhub\modules\notification\models\Notification;
use humhub\modules\stream\models\WallStreamQuery;
use humhub\modules\stream\widgets\WallStreamFilterNavigation;
use Yii;
use yii\base\BaseObject;

/**
 * Description of Events
 *
 * @author David Born ([staxDB](https://github.com/staxDB))
 */
class Events extends BaseObject
{
    const FILTER_BLOCK_ANNOUNCEMENT = 'announcement';
    const FILTER_ANNOUNCEMENT = 'announcement';

    public static function onWallEntryControlsInit($event)
    {
        $object = $event->sender->object;

        if (!$object instanceof Announcement) {
            return;
        }

        if ($object->content->canEdit()) {
            $event->sender->addWidget(CloseButton::class, [
                'announcement' => $object
            ]);
        }

        if ($object->content->canEdit()) {
            $event->sender->addWidget(ExportButton::class, [
                'announcement' => $object
            ]);
        }

        if ($object->isResetAllowed()) {
            $event->sender->addWidget(ResetButton::class, [
                'announcement' => $object
            ]);
        }

        if ($object->isResetStatisticsAllowed()) {
            $event->sender->addWidget(ResetStatisticsButton::class, [
                'announcement' => $object
            ]);
        }
    }

    public static function onStreamFilterBeforeRun($event)
    {
        $contentContainer = ContentContainerHelper::getCurrent();
        if (isset($contentContainer) && !$contentContainer->moduleManager->isEnabled('announcements')) {
            return;
        }
        $settings = EditForm::instantiate();
        if (!$settings->showFilters) {
            return;
        }
        /** @var $wallFilterNavigation WallStreamFilterNavigation */
        $wallFilterNavigation = $event->sender;

        // Add a new filter block to the last filter panel
        $wallFilterNavigation->addFilterBlock(static::FILTER_BLOCK_ANNOUNCEMENT, [
            'title' => Yii::t('AnnouncementsModule.base', 'Announcements'),
            'sortOrder' => 400
        ], WallStreamFilterNavigation::PANEL_POSITION_LEFT);

        // Add the filter to the new filter block
        $wallFilterNavigation->addFilter([
            'id' => AnnouncementStreamFilter::FILTER_NOT_READ,
            'title' => Yii::t('AnnouncementsModule.models', 'Not read by me'),
            'sortOrder' => 100
        ], static::FILTER_BLOCK_ANNOUNCEMENT);

        // Add the filter to the new filter block
        $wallFilterNavigation->addFilter([
            'id' => AnnouncementStreamFilter::FILTER_CLOSED,
            'title' => Yii::t('AnnouncementsModule.models', 'Old Announcement'),
            'sortOrder' => 200
        ], static::FILTER_BLOCK_ANNOUNCEMENT);
    }

    public static function onStreamFilterBeforeFilter($event)
    {
        $contentContainer = ContentContainerHelper::getCurrent();
        if (isset($contentContainer) && !$contentContainer->moduleManager->isEnabled('announcements')) {
            return;
        }
        $settings = EditForm::instantiate();
        if (!$settings->showFilters) {
            return;
        }
        /** @var $streamQuery WallStreamQuery */
        $streamQuery = $event->sender;

        // Add a new filterHandler to WallStreamQuery
        $streamQuery->filterHandlers[] = AnnouncementStreamFilter::class;
    }

    /**
     * @param $event
     * @throws \yii\base\Exception
     */
    public static function onMemberAdded($event)
    {
        $space = $event->space;

        if ($space->isModuleEnabled('announcement')) {
            // Add member to open announcements
            $announcements = Announcement::find()->contentContainer($space)->all();

            if (isset($announcements) && $announcements !== null) {
                foreach ($announcements as $announcement) {
                    if ($announcement->closed) {
                        continue;
                    }
                    $announcement->setConfirmation($event->user);
                }
            }
        }
    }

    /**
     * @param $event
     * @throws \Exception
     * @throws \yii\base\Exception
     * @throws \yii\db\StaleObjectException
     */
    public static function onMemberRemoved($event)
    {
        $space = $event->space;

        if ($space->isModuleEnabled('announcement')) {
            $announcements = Announcement::find()->contentContainer($space)->all();

            if (isset($announcements) && $announcements !== null) {
                foreach ($announcements as $announcement) {
                    $announcementUser = $announcement->findAnnouncementUser($event->user);

                    if ($announcement->closed) { // Skip closed announcements, because we want user to be part of statistics
                        if (isset($announcementUser) && $announcementUser !== null) {
                            $announcementUser->followContent(false); // But he shouldn't get any notifications about the content
                            continue;
                        }
                    }
                    if (isset($announcementUser) && $announcementUser !== null) {
                        $announcement->unlink('confirmations', $announcementUser, true);
                    }

                    // remove notifications
                    $notifications = Notification::find()->where(['source_class' => Announcement::class, 'source_pk' => $announcement->id, 'space_id' => $event->space->id])->all();
                    foreach ($notifications as $notification) {
                        $notification->delete();
                    }
                }
            }
        }
    }

    /**
     * On build of a Space Navigation, check if this module is enabled.
     * When enabled add a menu item
     *
     * @param type $event
     */
    public static function onSpaceMenuInit($event)
    {
        $space = $event->sender->space;

        // Is Module enabled on this workspace and is user member of space?
        if ($space->isModuleEnabled('announcements') && $space->isMember()) {
            $event->sender->addItem([
                'label' => Yii::t('AnnouncementsModule.base', 'Announcements'),
                'group' => 'modules',
                'url' => $space->createUrl('/announcements/announcement/show'),
                'icon' => '<i class="fa fa-bullhorn"></i>',
                'isActive' => (Yii::$app->controller->module && Yii::$app->controller->module->id == 'announcements'),
            ]);
        }
    }

    /**
     * On User delete, delete all announcements connected to this user
     *
     * @param type $event
     */
    public static function onUserDelete($event)
    {
        foreach (AnnouncementUser::findAll(['user_id' => $event->sender->id]) as $user) {
            $user->delete();
        }

        return true;
    }

    /**
     * Callback to validate module database records.
     *
     * @param Event $event
     * @throws \Exception
     * @throws \yii\db\StaleObjectException
     */
    public static function onIntegrityCheck($event)
    {
        $integrityController = $event->sender;
        $integrityController->showTestHeadline('Announcements Module - Users (' . AnnouncementUser::find()->count() . ' entries)');
        foreach (AnnouncementUser::find()->joinWith('announcement')->all() as $announcementUser) {
            if ($announcementUser->announcement === null) {
                if ($integrityController->showFix('Deleting announcement user id ' . $announcementUser->id . ' without existing announcement!')) {
                    $announcementUser->delete();
                }
            }

            if ($announcementUser->user === null) {
                if ($integrityController->showFix('Deleting announcement user id ' . $announcementUser->id . ' without existing user!')) {
                    $announcementUser->delete();
                }
            }
        }
    }

}
