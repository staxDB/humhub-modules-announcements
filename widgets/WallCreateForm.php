<?php

namespace humhub\modules\announcements\widgets;

use humhub\modules\content\components\ContentActiveRecord;
use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\space\models\Space;
use humhub\modules\content\models\Content;
use humhub\modules\announcements\permissions\CreateAnnouncement;
use humhub\modules\content\permissions\CreatePublicContent;
use humhub\modules\file\handler\FileHandlerCollection;

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

        if ($this->contentContainer->visibility !== Space::VISIBILITY_NONE && $this->contentContainer->can(CreatePublicContent::class)) {
            $defaultVisibility = $this->contentContainer->getDefaultContentVisibility();
            $canSwitchVisibility = true;
        } else {
            $defaultVisibility = Content::VISIBILITY_PRIVATE;
            $canSwitchVisibility = false;
        }

        $fileHandlerImport = FileHandlerCollection::getByType(FileHandlerCollection::TYPE_IMPORT);
        $fileHandlerCreate = FileHandlerCollection::getByType(FileHandlerCollection::TYPE_CREATE);

        return $this->render('@announcements/widgets/views/wallCreateContentForm', array(
            'form' => $this->renderForm(),
            'contentContainer' => $this->contentContainer,
            'submitUrl' => $this->contentContainer->createUrl($this->submitUrl),
            'submitButtonText' => $this->submitButtonText,
            'defaultVisibility' => $defaultVisibility,
            'canSwitchVisibility' => $canSwitchVisibility,
            'fileHandlers' => array_merge($fileHandlerCreate, $fileHandlerImport),
        ));

        if ($this->contentContainer instanceof Space) {

            if (!$this->contentContainer->permissionManager->can(new CreateAnnouncement())) {
                return;
            }
        }

        return parent::run();
    }

}

?>