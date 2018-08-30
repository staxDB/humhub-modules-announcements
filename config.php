<?php

use humhub\modules\announcements\Events;
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
    'class' => Module::class,
    'namespace' => 'humhub\modules\announcements',
    'events' => [
        ['class' => WallEntryControls::class, 'event' => WallEntryControls::EVENT_INIT, 'callback' => [Events::class, 'onWallEntryControlsInit']],
        ['class' => User::class, 'event' => User::EVENT_BEFORE_DELETE, 'callback' => [Events::class, 'onUserDelete']],
        ['class' => Menu::class, 'event' => Menu::EVENT_INIT, 'callback' => [Events::class, 'onSpaceMenuInit']],
        ['class' => Membership::class, 'event' => Membership::EVENT_MEMBER_ADDED, 'callback' => [Events::class, 'onMemberAdded']],
        ['class' => Membership::class, 'event' => Membership::EVENT_MEMBER_REMOVED, 'callback' => [Events::class, 'onMemberRemoved']],
        ['class' => IntegrityController::class, 'event' => IntegrityController::EVENT_ON_RUN, 'callback' => [Events::class, 'onIntegrityCheck']],
        ['class' => WallStreamQuery::class, 'event' =>  WallStreamQuery::EVENT_BEFORE_FILTER, 'callback' => [Events::class, 'onStreamFilterBeforeFilter']],
        ['class' => WallStreamFilterNavigation::class, 'event' =>  WallStreamFilterNavigation::EVENT_BEFORE_RUN, 'callback' => [Events::class, 'onStreamFilterBeforeRun']],
    ],
];
