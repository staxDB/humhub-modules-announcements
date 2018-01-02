<?php

use humhub\widgets\ActiveForm;
use yii\helpers\Html;

/* @var $announcement \humhub\modules\announcements\models\Announcement */
/* @var $user \humhub\modules\user\models\User */
/* @var $contentContainer \humhub\modules\content\models\ContentContainer */

?>

<div class="content_edit input-container" id="comment_edit_<?= $announcement->id; ?>" data-message="<?= $announcement->id ?>" data-content-component="announcements.Message" data-content-key="<?= $announcement->content->id ?>" >
    <div  class="alert alert-danger" role="alert" style="display:none">
        <span class="errorMessage"></span>
    </div>

    <?php $form = ActiveForm::begin(); ?>


    <?= \humhub\widgets\RichtextField::widget([
        'form' => $form,
        'model' => $announcement,
        'attribute' => 'message',
        'disabled' => $announcement->closed,
        'placeholder' => Yii::t('AnnouncementsModule.base', 'Edit your message')
    ]); ?>


    <div class="comment-buttons">

        <?=
        \humhub\modules\file\widgets\UploadButton::widget([
            'id' => 'announcement_upload_' . $announcement->id,
            'model' => $announcement,
            'dropZone' => '#announcement_'.$announcement->id,
            'preview' => '#announcement_upload_preview_'.$announcement->id,
            'progress' => '#announcement_upload_progress_'.$announcement->id,
            'max' => Yii::$app->getModule('content')->maxAttachedFiles
        ]);
        ?>

    <a href="#" class="btn btn-default btn-sm btn-comment-submit"
       data-action-click="editSubmit"
       data-action-url="<?= $announcement->content->container->createUrl('/announcements/announcement/edit', ['id' => $announcement->id]) ?>"
       data-action-submit
       data-ui-loader>
        <?= Yii::t('AnnouncementsModule.base', "Save") ?>
    </a>

    </div>

    <div id="announcement_upload_progress_<?= $announcement->id ?>" style="display:none; margin:10px 0;"></div>

    <?=
    \humhub\modules\file\widgets\FilePreview::widget([
        'id' => 'announcement_upload_preview_'.$announcement->id,
        'options' => ['style' => 'margin-top:10px'],
        'model' => $announcement,
        'edit' => true
    ]);
    ?>

    <?php ActiveForm::end(); ?>
</div>