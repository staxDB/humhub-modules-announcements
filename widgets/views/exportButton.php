<?php

/**
 * @var string $csv
 * @var string $xlsx
 */

use humhub\widgets\Button;

?>

<li><?= Button::asLink(Yii::t('AnnouncementsModule.widgets', 'Export in excel'), $announcement->content->container->createUrl('/announcements/announcement/export', ['announcementId' => $announcement->id, 'format'=>'xlsx']))->pjax(false)
        ->icon('fa-file-excel-o')->sm() ?></li>
