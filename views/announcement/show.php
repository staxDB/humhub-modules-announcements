<?php

use humhub\modules\announcements\widgets\WallCreateForm;
use humhub\modules\announcements\permissions\CreateAnnouncement;
use humhub\modules\stream\widgets\StreamViewer;

echo WallCreateForm::widget([
    'contentContainer' => $contentContainer,
    'submitButtonText' => Yii::t('AnnouncementsModule.base', 'Save'),
]);

$canCreateAnnouncements = $contentContainer->permissionManager->can(new CreateAnnouncement());

echo StreamViewer::widget([
    'contentContainer' => $contentContainer,
    'streamAction' => '/announcements/announcement/stream',
    'messageStreamEmpty' => ($canCreateAnnouncements) ?
        Yii::t('AnnouncementsModule.views', '<b>There are no announcements yet!</b><br>Be the first and create one...') :
        Yii::t('AnnouncementsModule.views', '<b>There are no announcements yet!</b>'),
    'messageStreamEmptyCss' => ($canCreateAnnouncements) ? 'placeholder-empty-stream' : '',
    'filters' => [
        'announcement_notAnswered' => Yii::t('AnnouncementsModule.views', 'Not read'),
        'filter_entry_files' => Yii::t('ContentModule.widgets_views_stream', 'Content with attached files'),
        'filter_entry_mine' => Yii::t('ContentModule.widgets_views_stream', 'Created by me'),
        'filter_entry_archived' => Yii::t('ContentModule.widgets_views_stream', 'Include archived posts'),
        'filter_visibility_public' => Yii::t('ContentModule.widgets_views_stream', 'Only public posts'),
        'filter_visibility_private' => Yii::t('ContentModule.widgets_views_stream', 'Only private posts')
    ]
]);
