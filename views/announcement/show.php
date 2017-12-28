<?php

echo \humhub\modules\announcements\widgets\WallCreateForm::widget([
    'contentContainer' => $contentContainer,
    'submitButtonText' => Yii::t('AnnouncementsModule.base', 'Save'),
]);
?>

<?php

$canCreateAnnouncements = $contentContainer->permissionManager->can(new \humhub\modules\announcements\permissions\CreateAnnouncement());


echo \humhub\modules\stream\widgets\StreamViewer::widget([
    'contentContainer' => $contentContainer,
    'streamAction' => '/announcements/message/stream',
    'messageStreamEmpty' => ($canCreateAnnouncements) ?
        Yii::t('AnnouncementsModule.base', '<b>There are no messages yet!</b><br>Be the first and create one...') :
        Yii::t('AnnouncementsModule.base', '<b>There are no messages yet!</b>'),
    'messageStreamEmptyCss' => ($canCreateAnnouncements) ? 'placeholder-empty-stream' : '',
    'filters' => [
        'filter_messages_notAnswered' => Yii::t('AnnouncementsModule.base', 'No confirmations yet'),
        'filter_entry_mine' => Yii::t('AnnouncementsModule.base', 'Created by me'),
        'filter_visibility_public' => Yii::t('AnnouncementsModule.base', 'Only public messages'),
        'filter_visibility_private' => Yii::t('AnnouncementsModule.base', 'Only private messages')
    ]
]);
?>
