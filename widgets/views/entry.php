<?php

use yii\helpers\Html;

/* @var $announcement \humhub\modules\announcements\models\Announcement */
/* @var $user \humhub\modules\user\models\User */
/* @var $contentContainer \humhub\modules\content\models\ContentContainer */

humhub\modules\announcements\assets\AnnouncementsAsset::register($this);
?>

<div data-announcement="<?= $announcement->id ?>" data-content-component="announcements.Message" data-content-key="<?= $announcement->content->id ?>">

    <?php if ($announcement->closed) : ?>
        &nbsp;<span class="label label-danger pull-right"><?= Yii::t('AnnouncementsModule.base', 'Old') ?></span>
    <?php endif; ?>

    <?= Html::beginForm($contentContainer->createUrl('/announcements/announcement/confirm', ['announcementId' => $announcement->id])); ?>

<!--    TODO: remove AddMessageInput-->

    <div data-ui-markdown>
        <?= humhub\widgets\RichText::widget(['text' => $announcement->message, 'record' => $announcement, 'markdown' => true]); ?>
    </div>

    <br><br>

    <?= $this->render('_answer', ['announcement' => $announcement, 'contentContainer' => $contentContainer]); ?>

    <div class="clearFloats"></div>

    <?= Html::endForm(); ?>
</div>