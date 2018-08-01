<?php

use humhub\modules\announcements\widgets\WallCreateForm;
use humhub\modules\announcements\permissions\CreateAnnouncement;
use humhub\modules\stream\widgets\StreamViewer;


$canCreateAnnouncements = $contentContainer->permissionManager->can(new CreateAnnouncement());
?>

<?=  WallCreateForm::widget([
    'contentContainer' => $contentContainer,
    'submitButtonText' => Yii::t('AnnouncementsModule.base', 'Save'),
]); ?>


<?php
 $filters = [
     'announcement_notAnswered' => Yii::t('AnnouncementsModule.views', 'Not read'),
     'announcement_mine' => Yii::t('AnnouncementsModule.views', 'Created by me'),
 ];
 if(version_compare(Yii::$app->version, '1.3', '>=')) {
     $filters['topic'] = null;
 }
?>

<?= StreamViewer::widget([
    'contentContainer' => $contentContainer,
    'streamAction' => '/announcements/announcement/stream',
    'messageStreamEmpty' => ($canCreateAnnouncements) ?
        Yii::t('AnnouncementsModule.views', '<b>There are no announcements yet!</b><br>Be the first and create one...') :
        Yii::t('AnnouncementsModule.views', '<b>There are no announcements yet!</b>'),
    'messageStreamEmptyCss' => ($canCreateAnnouncements) ? 'placeholder-empty-stream' : '',
    'filters' => $filters
]);
?>

