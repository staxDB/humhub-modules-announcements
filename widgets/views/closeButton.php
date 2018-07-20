<li>
    <?php if ($announcement->closed) : ?>
        <a href="#" data-action-click="close" data-action-target="[data-announcement='<?= $announcement->id ?>']" 
           data-action-url="<?= $announcement->content->container->createUrl('/announcements/announcement/open', ['id' => $announcement->id]); ?>">
            <i class="fa fa-check"></i>
            <?= Yii::t('AnnouncementsModule.widgets', 'Reopen Announcement') ?>
        </a>
    <?php else : ?>
        <a data-action-click="close" data-action-target="[data-announcement='<?= $announcement->id ?>']"
           data-action-url="<?= $announcement->content->container->createUrl('/announcements/announcement/close', ['id' => $announcement->id]); ?>">
            <i class="fa fa-times"></i>
            <?= Yii::t('AnnouncementsModule.widgets', 'Complete Announcement') ?>
        </a>
    <?php endif; ?>
</li>
