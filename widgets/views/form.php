<?php

use yii\helpers\Html;
use humhub\widgets\ActiveForm;

\humhub\modules\announcements\assets\AnnouncementsAsset::register($this);
?>

<?= \humhub\widgets\RichtextField::widget([
    'name' => 'message',
    'placeholder' => Yii::t('AnnouncementsModule.base', 'Add Announcement...'),
]); ?>


<!--// show hints-->
<div class="contentForm_options" data-content-component="announcements.Announcement">
    <p class="help-block"><?= Yii::t('AnnouncementsModule.base', 'Note: You can use markdown syntax. (For more information visit <a target="_blank" href="https://github.com/adam-p/markdown-here/wiki/Markdown-Cheatsheet">this link</a>)'); ?></p>
</div>