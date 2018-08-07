<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2017 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */
/* @var $this yii\web\View */
/* @var $model \humhub\modules\announcements\models\EditForm */

use humhub\widgets\Button;
use humhub\modules\ui\form\widgets\ActiveForm;
use yii\helpers\Url;
?>

<div class="panel panel-default">

    <div class="panel-heading">
        <?= Yii::t('AnnouncementsModule.forms', '<strong>Announcement</strong> module configuration'); ?>
    </div>

    <div class="panel-body">
        <?php $form = ActiveForm::begin(); ?>

        <h5>
            <?= Yii::t('AnnouncementsModule.forms', 'Settings for notifications'); ?>
        </h5>

        <?= $form->field($model, 'notifyCreated')->checkbox(); ?>
        <?= $form->field($model, 'notifyUpdated')->checkbox(); ?>
        <?= $form->field($model, 'notifyClosed')->checkbox(); ?>
        <?= $form->field($model, 'notifyResetStatistics')->checkbox(); ?>
        <br />

        <h5>
            <?= Yii::t('AnnouncementsModule.forms', 'Settings for filters'); ?>
        </h5>
        <?= $form->field($model, 'showFilters')->checkbox(); ?>
        <br />

        <h5>
            <?= Yii::t('AnnouncementsModule.forms', 'Settings to move content'); ?>
        </h5>
        <?= $form->field($model, 'setClosed')->checkbox(); ?>

        <?= Button::save()->submit(); ?>
        <?= Button::back(Url::to(['/admin/module'])); ?>

        <?php ActiveForm::end(); ?>
    </div>
</div>
