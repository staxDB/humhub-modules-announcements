<?php

namespace humhub\modules\announcements\controllers;

use humhub\modules\user\models\User;
use humhub\modules\user\widgets\UserListBox;
use humhub\modules\content\components\ContentContainerController;
use humhub\modules\announcements\models\Announcement;
use humhub\modules\announcements\models\AnnouncementUser;
use humhub\modules\announcements\widgets\WallCreateForm;
use humhub\modules\announcements\components\StreamAction;
use humhub\modules\stream\actions\Stream;
use humhub\modules\announcements\permissions\CreateAnnouncement;
use Yii;
use yii\web\HttpException;
use humhub\components\export\SpreadsheetExport;


/**
 * Controller for handling the Announcement-Models
 */
class AnnouncementController extends ContentContainerController
{

    public function actions()
    {
        return [
            'stream' => [
                'class' => StreamAction::class,
                'includes' => Announcement::class,
                'mode' => StreamAction::MODE_NORMAL,
                'contentContainer' => $this->contentContainer
            ],
        ];
    }

    /**
     * Shows the announcement tab
     */
    public function actionShow()
    {
        return $this->render('show', [
            'contentContainer' => $this->contentContainer
        ]);
    }

    /**
     * Posts a new announcement throu the announcement form
     *
     * @return type
     * @throws HttpException
     */
    public function actionCreate()
    {
        if (!$this->contentContainer->permissionManager->can(new CreateAnnouncement())) {
            throw new HttpException(400, 'Access denied!');
        }

        $announcement = new Announcement();
        $announcement->scenario = Announcement::SCENARIO_CREATE;
        $announcement->message = Yii::$app->request->post('message');

        return WallCreateForm::create($announcement, $this->contentContainer);
    }

    /**
     * Reloads a single entry
     */
    public function actionReload()
    {
        Yii::$app->response->format = 'json';
        $id = Yii::$app->request->get('id');
        $model = Announcement::findOne(['id' => $id]);

//        if (!$model) {
//            throw new HttpException(404);
//        }

        if (!$model->content->canRead()) {
            throw new HttpException(403);
        }

        return Stream::getContentResultEntry($model->content);
    }

    public function actionEdit()
    {
        $request = Yii::$app->request;
        $id = $request->get('id');

        $model = Announcement::findOne(['id' => $id]);

        if (!$model) {
            throw new HttpException(404);
        }

        $model->scenario = Announcement::SCENARIO_EDIT;

        if (!$model->content->canWrite()) {
            throw new HttpException(403);
        }

        if ($model->load($request->post())) {
            Yii::$app->response->format = 'json';
            $result = [];
            if ($model->validate() && $model->save()) {
                // Reload record to get populated updated_at field
                $model = Announcement::findOne(['id' => $id]);
                return Stream::getContentResultEntry($model->content);
            } else {
                $result['errors'] = $model->getErrors();
            }
            return $result;
        }

        return $this->renderAjax('edit', ['announcement' => $model]);
    }

    public function actionOpen()
    {
        return $this->asJson($this->setClosed(Yii::$app->request->get('id'), false));
    }

    public function actionClose()
    {
        return $this->asJson($this->setClosed(Yii::$app->request->get('id'), true));
    }

    public function setClosed($id, $closed)
    {
        $model = Announcement::findOne(['id' => $id]);
        if (!$model) {
            throw new HttpException(404);
        }
        $model->scenario = Announcement::SCENARIO_CLOSE;

        if ($model->content->isArchived()) {
            $model->content->unarchive();
        }

        if (!$model->content->canWrite()) {
            throw new HttpException(403, Yii::t('AnnouncementsModule.controller', 'Access denied!'));
        }

        $model->closed = $closed;
        $model->save();

        // Refresh updated_at
        $model->content->refresh();

        return Stream::getContentResultEntry($model->content);
    }

    public function actionResetStatistics()
    {
        return $this->asJson($this->resetAnnouncementStatistics());
    }

    public function resetAnnouncementStatistics()
    {
        $model = $this->getAnnouncementByParameter();
        if (!$model) {
            throw new HttpException(404);
        }
        $model->scenario = Announcement::SCENARIO_RESET;

        if ($model->content->isArchived()) {
            $model->content->unarchive();
        }

        if (!$model->isResetStatisticsAllowed()) {
            throw new HttpException(403);
        }

        $model->resetStatistics();

        // Refresh updated_at
        $model->content->refresh();
        // save model to send notifications
        $model->save();

        return Stream::getContentResultEntry($model->content);
    }

    /**
     * Confirm an Announcement
     */
    public function actionConfirm()
    {
        Yii::$app->response->format = 'json';
        $announcement = $this->getAnnouncementByParameter();
        $announcement->confirm();

        return Stream::getContentResultEntry($announcement->content);
    }

    /**
     * Resets users confirmation
     */
    public function actionConfirmationReset()
    {
        Yii::$app->response->format = 'json';
        $announcement = $this->getAnnouncementByParameter();
        $announcement->resetConfirmation();

        return Stream::getContentResultEntry($announcement->content);
    }

    /**
     * Returns a user list including the pagination which contains all results
     * for an answer
     */
    public function actionUserListConfirmed()
    {
        $announcement = $this->getAnnouncementByParameter();

        if ($announcement == null) {
            throw new HttpException(401, Yii::t('AnnouncementsModule.controller', 'Announcement not found!'));
        }

        $query = User::find();
        $query->leftJoin('announcement_user', 'announcement_user.user_id=user.id');
        $query->andWhere(['announcement_user.announcement_id' => $announcement->id]);
        $query->andWhere(['announcement_user.confirmed' => true]);
        //$query->orderBy('announcement_user.created_at DESC');

        $title = Yii::t('AnnouncementsModule.controller', 'Users who read this <strong>{title}</strong>', ['{title}' => Yii::t('AnnouncementsModule.base', 'Announcement')]);

        return $this->renderAjaxContent(UserListBox::widget(['query' => $query, 'title' => $title]));
    }

    /**
     * Returns a user list including the pagination which contains all results
     * for an answer
     */
    public function actionUserListUnconfirmed()
    {
        $announcement = $this->getAnnouncementByParameter();

        if ($announcement == null) {
            throw new HttpException(401, Yii::t('AnnouncementsModule.controller', 'Announcement not found!'));
        }

        $query = User::find();
        $query->leftJoin('announcement_user', 'announcement_user.user_id=user.id');
        $query->andWhere(['announcement_user.announcement_id' => $announcement->id]);
        $query->andWhere(['announcement_user.confirmed' => false]);
        //$query->orderBy('announcement_user.created_at DESC');
        $title = Yii::t('AnnouncementsModule.controller', 'Users didn\'t read this <strong>{title}</strong>', ['{title}' => Yii::t('AnnouncementsModule.base', 'Announcement')]);

        return $this->renderAjaxContent(UserListBox::widget(['query' => $query, 'title' => $title]));
    }



    /**
     * Export user list as csv or xlsx
     * @param string $format supported format by phpspreadsheet
     * @return \yii\web\Response
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     * @throws \yii\base\Exception
     */
    public function actionExport()
    {
        $format = Yii::$app->request->get('format');
        $announcement = $this->getAnnouncementByParameter();

        if ($announcement == null) {
            throw new HttpException(401, Yii::t('AnnouncementsModule.controller', 'Announcement not found!'));
        }

        $query = AnnouncementUser::find();
        $query->where(['announcement_user.announcement_id' => $announcement->id]);
        $query->orderBy(['announcement_user.confirmed' => 'ASC']);

        $exporter = new SpreadsheetExport([
            'showHeader' => true,
            'query' => $query,
            'columns' => $this->collectExportColumns(),
            'resultConfig' => [
                'fileBaseName' => 'announcementID_'.$announcement->id . ' - ' . date("d.m.Y_H-m-s"),
                'writerType' => $format,
            ],
        ]);

        return $exporter->export()->send();
    }

    /**
     * Return array with columns for data export
     * @return array
     */
    private function collectExportColumns()
    {
        $userColumns = [
            'user.id',
            'user.username',
            'user.profile.firstname',
            'user.profile.lastname',
            'user.email',
            'confirmed',
        ];

        return $userColumns;
    }

    /**
     * Returns a given confirmMessage by given request parameter.
     *
     * This method also validates access rights of the requested poll object.
     */
    private function getAnnouncementByParameter()
    {

        $announcementId = (int) Yii::$app->request->get('announcementId');

        $announcement = Announcement::find()->contentContainer($this->contentContainer)->readable()->where(['announcement.id' => $announcementId])->one();

        if ($announcement == null) {
            throw new HttpException(401, Yii::t('AnnouncementsModule.controller', 'Could not load Announcement!'));
        }

        if (!$announcement->content->canRead()) {
            throw new HttpException(403, Yii::t('AnnouncementsModule.controller', 'You have insufficient permissions to perform that operation!'));
        }

        return $announcement;
    }

}
