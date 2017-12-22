<?php

use humhub\widgets\ActiveForm;

/* @var $announcement \humhub\modules\announcements\models\Announcement */
/* @var $user \humhub\modules\user\models\User */
/* @var $contentContainer \humhub\modules\content\models\ContentContainer */

?>

<div data-message="<?= $announcement->id ?>" data-content-component="announcements.Message" data-content-key="<?= $announcement->content->id ?>" class="content_edit" id="message_edit_<?= $announcement->id; ?>">
    <div  class="alert alert-danger" role="alert" style="display:none">
        <span class="errorMessage"></span>
    </div>

    <?php $form = ActiveForm::begin(); ?>

    <?= \humhub\widgets\RichtextField::widget([
        'form' => $form,
        'model' => $announcement,
        'label' => Yii::t('AnnouncementsModule.base', 'Message'),
        'attribute' => "message",
        'disabled' => $announcement->closed,
        'placeholder' => Yii::t('AnnouncementsModule.base', 'Edit your message')
    ]); ?>

    <a href="#" class="btn btn-primary"
       data-action-click="editSubmit" data-action-submit
       data-action-url="<?= $announcement->content->container->createUrl('/announcements/message/edit', ['id' => $announcement->id]) ?>"
       data-ui-loader>
        <?= Yii::t('AnnouncementsModule.base', "Save") ?>
    </a>

    <a href="#" class="btn btn-danger"
       data-action-click="editCancel"
       data-action-url="<?= $announcement->content->container->createUrl('/announcements/message/reload', ['id' => $announcement->id]) ?>"
       data-ui-loader>
        <?= Yii::t('AnnouncementsModule.base', "Cancel") ?>
    </a>
    <?php ActiveForm::end(); ?>
</div>