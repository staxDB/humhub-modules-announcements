<?php

use yii\helpers\Html;
use humhub\widgets\ActiveForm;

\humhub\modules\announcements\assets\AnnouncementsAsset::register($this);

?>

<?= \humhub\widgets\RichtextField::widget([
    'name' => 'title',
    'placeholder' => Yii::t('AnnouncementsModule.base', 'Title')
]); ?>

<div class="contentForm_options" data-content-component="polls.Poll">
    <?= humhub\modules\announcements\widgets\AddMessageInput::widget(['name' => 'message', 'showTitle' => false]); ?>
</div>