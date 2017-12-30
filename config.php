<?php


use humhub\modules\space\widgets\Menu;
use humhub\modules\user\models\User;
use humhub\modules\space\models\Membership;
use humhub\commands\IntegrityController;
use humhub\modules\content\widgets\WallEntryControls;

return [
	'id' => 'announcements',
	'class' => 'humhub\modules\announcements\Module',
	'namespace' => 'humhub\modules\announcements',
	'events' => [
        ['class' => WallEntryControls::className(), 'event' => WallEntryControls::EVENT_INIT, 'callback' => ['humhub\modules\announcements\Events', 'onWallEntryControlsInit']],
        ['class' => User::className(), 'event' => User::EVENT_BEFORE_DELETE, 'callback' => ['humhub\modules\announcements\Events', 'onUserDelete']],
        ['class' => Menu::className(), 'event' => Menu::EVENT_INIT, 'callback' => ['humhub\modules\announcements\Events', 'onSpaceMenuInit']],
        ['class' => Membership::className(), 'event' => Membership::EVENT_MEMBER_ADDED, 'callback' => ['humhub\modules\announcements\Events', 'onMemberAdded']],
        ['class' => Membership::className(), 'event' => Membership::EVENT_MEMBER_REMOVED, 'callback' => ['humhub\modules\announcements\Events', 'onMemberRemoved']],
        ['class' => IntegrityController::className(), 'event' => IntegrityController::EVENT_ON_RUN, 'callback' => ['humhub\modules\announcements\Events', 'onIntegrityCheck']],
    ],
];
?>

