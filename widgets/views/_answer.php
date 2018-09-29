<?php

use yii\helpers\Html;

/* @var $announcement \humhub\modules\announcements\models\Announcement */
/* @var $contentContainer \humhub\modules\content\models\ContentContainer */

humhub\modules\announcements\assets\AnnouncementsAsset::register($this);
?>

<?php if ($announcement->canShowStatistics()) : ?>
    <div class="announcement-media">
<?php endif; ?>

<?php if (!$announcement->hasUserConfirmed() && $announcement->findAnnouncementUser() && !Yii::$app->user->isGuest && !$announcement->closed) : ?>
    <div id="confirm-button" class="alert alert-info" style="margin-top:1px;">
        <?= Html::checkBox('checked', false, ['class' => 'tt', 'label' => Yii::t('AnnouncementsModule.widgets', 'Mark as read'), 'announcementId' => $announcement->id, 'data-action-change' => 'confirm', 'data-action-submit', 'data-ui-loader']); ?>
    </div>
 <?php elseif ($announcement->hasUserConfirmed() && $announcement->findAnnouncementUser() && !Yii::$app->user->isGuest && !$announcement->closed) : ?>
    <div>
        <i class="fa fa-check" aria-hidden="true" style="color: #bebebe;"></i>
        <i style="color: #bebebe;"><?= Yii::t('AnnouncementsModule.widgets', 'Marked as read');?></i>
    </div>
<?php endif; ?>

<?php if ($announcement->canShowStatistics()) : ?>
    <div class="media" id="announcement_statistics">
        <div style="padding-left:10px; border-left:3px solid">
            <div class="media-left media-middle">
                <i class="fa fa-line-chart colorDefault" style="font-size:35px;"></i>
            </div>
            <div class="media-body clearfix">
                <div class="col-md-12">
                    <div class="media-heading">
                        <strong><?= Yii::t('AnnouncementsModule.widgets', 'Statistic:') ?></strong>

                        <!-- confirmed users-->
                        <?php
                        $userlist = ''; // Variable for users output
                        $maxUser = 10; // Limit for rendered users inside the tooltip

                        foreach ($announcement->confirmedUsers as $confirmedUserKey => $confirmedUser) {
                            if ($confirmedUserKey == $maxUser) {
                                // Output with the number of not rendered users
                                $userlist .= Yii::t('AnnouncementsModule.widgets', 'and {count} more read this.', ['{count}' => (intval($announcement->confirmedCount - $maxUser))]);
                                // Stop the loop
                                break;
                            } else {
                                $userlist .= Html::encode($confirmedUser->user->displayName) . "\n";
                            }
                        }

                        $announcementText = Yii::t('AnnouncementsModule.widgets', 'Read, ');
                        ?>
                        <span class="tt" data-toggle="tooltip" data-placement="top"
                              data-original-title="<?= $userlist; ?>">
                            <?php if ($announcement->confirmedCount > 0) { ?>
                                <a href="<?= $contentContainer->createUrl('/announcements/announcement/user-list-confirmed', ['announcementId' => $announcement->id]); ?>"
                                   data-target="#globalModal">
                                   <?= $announcement->confirmedCount . " " . $announcementText ?>
                                </a>
                            <?php } elseif ($announcement->confirmedCount > 0) { ?>
                                <?= $announcement->confirmedCount . " " . $announcementText ?>
                            <?php } else { ?>
                                0 <?= $announcementText ?>
                            <?php } ?>
                        </span>

                        <!--Unconfirmed Users-->
                        <?php
                        $userlist = ''; // Variable for users output
                        $maxUser = 10; // Limit for rendered users inside the tooltip

                        foreach ($announcement->unConfirmedUsers as $unConfirmedUserKey => $unConfirmedUser) {
                            if ($unConfirmedUserKey == $maxUser) {
                                // output with the number of not rendered users
                                $userlist .= Yii::t('AnnouncementsModule.widgets', 'and {count} more didn\'t read this.', ['{count}' => (intval($announcement->unConfirmedCount - $maxUser))]);
                                // stop the loop
                                break;
                            } else {
                                $userlist .= Html::encode($unConfirmedUser->user->displayName) . "\n";
                            }
                        }

                        $announcementText = Yii::t('AnnouncementsModule.widgets', 'Unread');
                        ?>
                        <span class="tt" data-toggle="tooltip" data-placement="top"
                              data-original-title="<?= $userlist; ?>">
                            <?php if ($announcement->unConfirmedCount > 0) { ?>
                                <a href="<?= $contentContainer->createUrl('/announcements/announcement/user-list-unconfirmed', ['announcementId' => $announcement->id]); ?>"
                                   data-target="#globalModal">
                                    <?= $announcement->unConfirmedCount . " " . $announcementText ?>
                                </a>
                            <?php } elseif ($announcement->unConfirmedCount > 0) { ?>
                                <?= $announcement->unConfirmedCount . " " . $announcementText ?>
                            <?php } else { ?>
                                0 <?= $announcementText ?>
                            <?php } ?>
                        </span>
                    </div>
                </div>

                <!-- Progress Bar -->
                <?php
                $percent = round($announcement->getPercent());
                $color = 'progress-bar-info';
                ?>

                <div class="col-md-5" style="padding-right: 30px;">
                    <div class="progress">
                        <div id="announcement_progress_<?= $announcement->id; ?>"
                             class="progress-bar <?= $color; ?>"
                             role="progressbar"
                             aria-valuenow="<?= $percent; ?>" aria-valuemin="0" aria-valuemax="100"
                             style="width:0%">
                        </div>
                    </div>
                    <script>
                        $('#announcement_progress_<?= $announcement->id; ?>').css('width', '<?= $percent; ?>%');
                    </script>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php if ($announcement->canShowStatistics()) : ?>
    </div>
<?php endif; ?>

<?php if (!$announcement->fileManager->find()->one()) : ?>
    <!-- Add a headline if no files are attached-->
    <hr>
<?php endif; ?>
