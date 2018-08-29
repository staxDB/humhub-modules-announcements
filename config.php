<?php

use humhub\modules\announcements\Module;
use humhub\modules\space\widgets\Menu;
use humhub\modules\user\models\User;
use humhub\modules\space\models\Membership;
use humhub\commands\IntegrityController;
use humhub\modules\content\widgets\WallEntryControls;
use humhub\modules\stream\models\WallStreamQuery;
use humhub\modules\stream\widgets\WallStreamFilterNavigation;

return [
    'id' => 'announcements',
    'class' => 'humhub\modules\announcements\Module',
    'namespace' => 'humhub\modules\announcements',
    'events' => [
        ['class' => WallEntryControls::class, 'event' => WallEntryControls::EVENT_INIT, 'callback' => ['humhub\modules\announcements\Events', 'onWallEntryControlsInit']],
        ['class' => User::class, 'event' => User::EVENT_BEFORE_DELETE, 'callback' => ['humhub\modules\announcements\Events', 'onUserDelete']],
        ['class' => Menu::class, 'event' => Menu::EVENT_INIT, 'callback' => ['humhub\modules\announcements\Events', 'onSpaceMenuInit']],
        ['class' => Membership::class, 'event' => Membership::EVENT_MEMBER_ADDED, 'callback' => ['humhub\modules\announcements\Events', 'onMemberAdded']],
        ['class' => Membership::class, 'event' => Membership::EVENT_MEMBER_REMOVED, 'callback' => ['humhub\modules\announcements\Events', 'onMemberRemoved']],
        ['class' => IntegrityController::class, 'event' => IntegrityController::EVENT_ON_RUN, 'callback' => ['humhub\modules\announcements\Events', 'onIntegrityCheck']],
        ['class' => WallStreamQuery::class, 'event' =>  WallStreamQuery::EVENT_BEFORE_FILTER, 'callback' => ['humhub\modules\announcements\Events', 'onStreamFilterBeforeFilter']],
        ['class' => WallStreamFilterNavigation::class, 'event' =>  WallStreamFilterNavigation::EVENT_BEFORE_RUN, 'callback' => ['humhub\modules\announcements\Events', 'onStreamFilterBeforeRun']],
    ],
];
