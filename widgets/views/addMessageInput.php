<?php
/* @var $name String */
/* @var $showTitle Boolean */

$title = ($showTitle) ? '0' . Yii::t('AnnouncementsModule.base', 'Announcement') : '';
?>

<?= \humhub\widgets\RichtextField::widget([
    'name' => $name,
    'placeholder' => Yii::t('AnnouncementsModule.base', 'Add Announcement...'),
    'label' => $title
]); ?>
<p class="help-block"><?= Yii::t('AnnouncementsModule.base', 'Note: You can use markdown syntax. (For more information visit http://www.markdown.de)'); ?></p>

