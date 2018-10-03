<?php

namespace humhub\modules\announcements\widgets;

use Yii;
use humhub\modules\content\widgets\WallCreateContentForm;
use humhub\modules\content\components\ContentActiveRecord;
use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\space\models\Space;
use humhub\modules\content\models\Content;
use humhub\modules\announcements\permissions\CreateAnnouncement;
use humhub\modules\content\permissions\CreatePublicContent;
use humhub\modules\file\handler\FileHandlerCollection;
use humhub\modules\stream\actions\Stream;
use humhub\modules\topic\models\Topic;

class WallCreateForm extends WallCreateContentForm
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

        if ($this->contentContainer->visibility !== Space::VISIBILITY_NONE && $this->contentContainer->can(CreatePublicContent::class)) {
            $defaultVisibility = $this->contentContainer->getDefaultContentVisibility();
            $canSwitchVisibility = true;
        } else {
            $defaultVisibility = Content::VISIBILITY_PRIVATE;
            $canSwitchVisibility = false;
        }

        $fileHandlerImport = FileHandlerCollection::getByType(FileHandlerCollection::TYPE_IMPORT);
        $fileHandlerCreate = FileHandlerCollection::getByType(FileHandlerCollection::TYPE_CREATE);

        return $this->render('@announcements/widgets/views/wallCreateContentForm', [
            'form' => $this->renderForm(),
            'contentContainer' => $this->contentContainer,
            'submitUrl' => $this->contentContainer->createUrl($this->submitUrl),
            'submitButtonText' => $this->submitButtonText,
            'defaultVisibility' => $defaultVisibility,
            'canSwitchVisibility' => $canSwitchVisibility,
            'fileHandlers' => array_merge($fileHandlerCreate, $fileHandlerImport),
        ]);
//        return parent::run();

    }

    /**
     * Creates the given ContentActiveRecord based on given submitted form information.
     *
     * - Automatically assigns ContentContainer
     * - Access Check
     * - User Notification / File Uploads
     * - Reloads Wall after successfull creation or returns error json
     *
     * [See guide section](guide:dev-module-stream.md#CreateContentForm)
     *
     * @param ContentActiveRecord $record
     * @return string json
     */
    public static function create(ContentActiveRecord $record, ContentContainerActiveRecord $contentContainer = null)
    {
        Yii::$app->response->format = 'json';

        $visibility = Yii::$app->request->post('visibility');
        if ($visibility == Content::VISIBILITY_PUBLIC && !$contentContainer->permissionManager->can(new CreatePublicContent())) {
            $visibility = Content::VISIBILITY_PRIVATE;
        }

        $record->content->visibility = $visibility;
        $record->content->container = $contentContainer;

        if ($record->save()) {
            $topics = Yii::$app->request->post('postTopicInput');
            if(!empty($topics)) {
                Topic::attach($record->content, $topics);
            }

            $record->fileManager->attach(Yii::$app->request->post('fileList'));
            return Stream::getContentResultEntry($record->content);
        }

        return ['errors' => $record->getErrors()];
    }

}
