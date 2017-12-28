<?php

namespace humhub\modules\announcements\widgets;

use humhub\modules\content\components\ContentActiveRecord;
use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\space\models\Space;
use humhub\modules\announcements\permissions\CreateAnnouncement;

class WallCreateForm extends \humhub\modules\content\widgets\WallCreateContentForm
{

    /**
     * @inheritdoc
     */
    public $submitUrl = '/announcements/announcement/create';

    /**
     * @inheritdoc
     */
    public function renderForm()
    {
        return $this->render('form', []);
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        if ($this->contentContainer instanceof Space) {

            if (!$this->contentContainer->permissionManager->can(new CreateAnnouncement())) {
                return;
            }
        }

        return parent::run();
    }

}

?>