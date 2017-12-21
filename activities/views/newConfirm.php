<?php

use yii\helpers\Html;

echo Yii::t('AnnouncementsModule.base', '{userName} has read {announcement}.', array(
    '{userName}' => '<strong>' . Html::encode($originator->displayName) . '</strong>',
    '{announcement}' => $this->context->getContentInfo($source)
));
?>