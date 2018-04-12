<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2017 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */
/* @var $this yii\web\View */
/* @var $model \humhub\modules\announcements\models\EditForm */

use humhub\widgets\Button;
use Yii;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
?>

<div class="panel panel-default">

    <div class="panel-heading">
        <?= Yii::t('AnnouncementsModule.forms', '<strong>Announcement</strong> module configuration'); ?>
    </div>

    <div class="panel-body">
        <?php $form = ActiveForm::begin(); ?>

        <h4>
            <?= Yii::t('AnnouncementsModule.forms', 'Global settings'); ?>
        </h4>

        <?= $form->field($model, 'notifyCreated')->checkbox(); ?>
        <?= $form->field($model, 'notifyUpdated')->checkbox(); ?>
        <?= $form->field($model, 'notifyClosed')->checkbox(); ?>
        <?= $form->field($model, 'notifyResetStatistics')->checkbox(); ?>

        <?= Button::save()->submit(); ?>
        <?= Button::back(Url::to(['/admin/module'])); ?>

        <?php ActiveForm::end(); ?>
    </div>
</div>
