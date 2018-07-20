<li>
    <a href="#" data-action-click="reset"
       data-action-target="[data-announcement='<?= $announcement->id ?>']"
       data-action-url="<?= $announcement->content->container->createUrl('/announcements/announcement/confirmation-reset', ['announcementId' => $announcement->id]); ?>">
        <i class="fa fa-undo"></i>
        <?= Yii::t('AnnouncementsModule.widgets', 'Mark as unread') ?>
    </a>
</li>