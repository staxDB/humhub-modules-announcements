<?php

use yii\helpers\Html;
use humhub\widgets\RichText;

/* @var $announcement \humhub\modules\announcements\models\Announcement */
/* @var $user \humhub\modules\user\models\User */
/* @var $contentContainer \humhub\modules\content\models\ContentContainer */

humhub\modules\announcements\assets\AnnouncementsAsset::register($this);
?>

<div data-announcement="<?= $announcement->id ?>" data-content-component="announcements.Message" data-content-key="<?= $announcement->content->id ?>">

    <?= Html::beginForm($contentContainer->createUrl('/announcements/announcement/confirm', ['announcementId' => $announcement->id])); ?>

    <div data-ui-markdown>
        <?= RichText::widget(['text' => $announcement->message, 'record' => $announcement, 'markdown' => true]); ?>
    </div>

    <?= $this->render('_answer', ['announcement' => $announcement, 'contentContainer' => $contentContainer]); ?>

    <div class="clearFloats"></div>

    <?= Html::endForm(); ?>
</div>
