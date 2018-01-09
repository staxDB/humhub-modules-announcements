<?php

use yii\helpers\Html;
use humhub\widgets\ActiveForm;
use humhub\modules\announcements\assets\AnnouncementsAsset;
use humhub\widgets\RichtextField;

AnnouncementsAsset::register($this);
?>

<?= RichtextField::widget([
    'name' => 'message',
    'placeholder' => Yii::t('AnnouncementsModule.widgets', 'Add Announcement...'),
]); ?>


<!--// show hints-->
<div class="contentForm_options" data-content-component="announcements.Announcement">
    <p class="help-block"><?= Yii::t('AnnouncementsModule.widgets', 'Note: You can use markdown syntax. (For more information visit <a target="_blank" href="https://github.com/adam-p/markdown-here/wiki/Markdown-Cheatsheet">this link</a>)'); ?></p>
</div>