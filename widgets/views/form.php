<?php

use humhub\modules\announcements\assets\AnnouncementsAsset;
//use humhub\modules\content\widgets\richtext\RichTextField;
use humhub\widgets\RichtextField;


AnnouncementsAsset::register($this);

?>

<?= RichtextField::widget([
    'pluginOptions' => ['maxHeight' => '300px'],
    'placeholder' => Yii::t('AnnouncementsModule.widgets', 'Add Announcement...'),
    'name' => 'message',
    'layout' => RichTextField::LAYOUT_INLINE,
    ]);
?>

<!-- Show hints -->
<div class="contentForm_options" data-content-component="announcements.Announcement">
    <p class="help-block"><?= Yii::t('AnnouncementsModule.widgets', 'Note: You can use markdown syntax. (For more information visit <a target="_blank" href="https://github.com/adam-p/markdown-here/wiki/Markdown-Cheatsheet">this link</a>)'); ?></p>
</div>
