<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\announcements\permissions;

use humhub\modules\space\models\Space;

/**
 * CreatePost Permission
 */
class CreateAnnouncement extends \humhub\libs\BasePermission
{

    /**
     * @inheritdoc
     */
    public $defaultAllowedGroups = [
        Space::USERGROUP_OWNER,
        Space::USERGROUP_ADMIN,
        Space::USERGROUP_MODERATOR,
        Space::USERGROUP_MEMBER,
    ];

    /**
     * @inheritdoc
     */
    protected $fixedGroups = [
        Space::USERGROUP_USER
    ];

    /**
     * @inheritdoc
     */
    protected $title = "Create Announcement";

    /**
     * @inheritdoc
     */
    protected $description = "Allows the user to create an Announcement";

    /**
     * @inheritdoc
     */
    protected $moduleId = 'announcements';

}
