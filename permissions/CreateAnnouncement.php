<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\announcements\permissions;

use Yii;
use humhub\modules\space\models\Space;
use humhub\libs\BasePermission;

/**
 * CreatePost Permission
 */
class CreateAnnouncement extends BasePermission
{

    /**
     * @inheritdoc
     */
    protected $moduleId = 'announcements';

    /**
     * @inheritdoc
     */
    public $defaultAllowedGroups = [
        Space::USERGROUP_OWNER,
        Space::USERGROUP_ADMIN,
        Space::USERGROUP_MODERATOR,
    ];

    /**
     * @inheritdoc
     */
    protected $fixedGroups = [
        Space::USERGROUP_USER
    ];

    public function getTitle()
    {
        return Yii::t('AnnouncementsModule.permissions', 'Create Announcement');
    }

    public function getDescription()
    {
        return Yii::t('AnnouncementsModule.permissions', 'Allows the user to create/edit an Announcement');
    }

}
