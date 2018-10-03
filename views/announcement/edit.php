<?php

use humhub\modules\ui\form\widgets\ActiveForm;
use humhub\modules\content\widgets\richtext\RichTextField;

/* @var $announcement \humhub\modules\announcements\models\Announcement */
/* @var $user \humhub\modules\user\models\User */
/* @var $contentContainer \humhub\modules\content\models\ContentContainer */

?>

<div class="content_edit input-container" id="comment_edit_<?= $announcement->id; ?>" data-message="<?= $announcement->id ?>" data-content-component="announcements.Message" data-content-key="<?= $announcement->content->id ?>" >
    <div class="alert alert-danger" role="alert" style="display:none">
        <span class="errorMessage"></span>
    </div>

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($announcement, 'message')->widget(RichTextField::class, [
        'pluginOptions' => ['maxHeight' => '300px'],
        'disabled' => $announcement->closed,
        'placeholder' => Yii::t('AnnouncementsModule.base', 'Edit your message')
    ])->label(false) ?>

    <a href="#" class="btn btn-primary"
       data-action-click="editSubmit" data-action-submit
       data-action-url="<?= $announcement->content->container->createUrl('/announcements/announcement/edit', ['id' => $announcement->id]) ?>"
       data-ui-loader>
        <?= Yii::t('AnnouncementsModule.views', 'Save') ?>
    </a>

    <a href="#" class="btn btn-danger"
       data-action-click="editCancel"
       data-action-url="<?= $announcement->content->container->createUrl('/announcements/announcement/reload', ['id' => $announcement->id]) ?>"
       data-ui-loader>
        <?= Yii::t('AnnouncementsModule.views', "Cancel") ?>
    </a>
    <?php ActiveForm::end(); ?>
</div>
