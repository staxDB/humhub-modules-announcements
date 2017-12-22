<li>
    <?php if ($announcement->closed) : ?>
        <a href="#" data-action-click="close" data-action-target="[data-announcement='<?= $announcement->id ?>']" 
           data-action-url="<?= $announcement->content->container->createUrl('/announcements/message/open', ['id' => $announcement->id]); ?>">
            <i class="fa fa-check"></i>
            <?= Yii::t('AnnouncementsModule.base', 'Reopen Announcement') ?>
        </a>
    <?php else : ?>
        <a data-action-click="close" data-action-target="[data-announcement='<?= $announcement->id ?>']"
           data-action-url="<?= $announcement->content->container->createUrl('/announcements/message/close', ['id' => $announcement->id]); ?>">
            <i class="fa fa-times"></i>
            <?= Yii::t('AnnouncementsModule.base', 'Complete Announcement') ?>
        </a>
    <?php endif; ?>
</li>
