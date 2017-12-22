<?php

namespace humhub\modules\announcements\controllers;

use humhub\components\mail\Message;
use Yii;
use yii\web\HttpException;
use yii\helpers\Html;
use humhub\modules\user\models\User;
use humhub\modules\user\widgets\UserListBox;
use humhub\modules\content\components\ContentContainerController;
use humhub\modules\announcements\models\Announcement;
use humhub\modules\announcements\models\AnnouncementUser;
use humhub\modules\announcements\widgets\WallCreateForm;
use humhub\modules\announcements\components\StreamAction;
use humhub\modules\stream\actions\Stream;
use humhub\modules\announcements\permissions\CreateAnnouncement;


/**
 * DefaultController implements the CRUD actions for Message model.
 */
class MessageController extends ContentContainerController
{

    public function actions()
    {
        return array(
            'stream' => array(
                'class' => StreamAction::className(),
                'includes' => Announcement::className(),
                'mode' => StreamAction::MODE_NORMAL,
                'contentContainer' => $this->contentContainer
            ),
        );
    }

    /**
     * Shows the questions tab
     */
    public function actionShow()
    {
        return $this->render('show', array(
            'contentContainer' => $this->contentContainer
        ));
    }

    /**
     * Posts a new announcement  throu the announcement form
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

        if (!$model->content->canRead()) {
            throw new HttpException(403, Yii::t('AnnouncementsModule.base', 'Access denied!'));
        }

        return \humhub\modules\stream\actions\Stream::getContentResultEntry($model->content);
    }

    public function actionEdit()
    {
        $request = Yii::$app->request;
        $id = $request->get('id');

        $edited = false;
        $model = Announcement::findOne(['id' => $id]);
        $model->scenario = Announcement::SCENARIO_EDIT;

        if (!$model->content->canWrite()) {
            throw new HttpException(403, Yii::t('AnnouncementsModule.base', 'Access denied!'));
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

        return $this->renderAjax('edit', ['announcement' => $model, 'edited' => $edited]);
    }

    public function actionOpen()
    {
        return $this->asJson($this->setClosed(Yii::$app->request->get('id'), false));
    }

    public function actionClose()
    {
        return  $this->asJson($this->setClosed(Yii::$app->request->get('id'), true));
    }

    public function setClosed($id, $closed)
    {
        $model = Announcement::findOne(['id' => $id]);
        $model->scenario = Announcement::SCENARIO_CLOSE;

        if (!$model->content->canWrite()) {
            throw new HttpException(403, Yii::t('AnnouncementsModule.base', 'Access denied!'));
        }

        $model->closed = $closed;
        $model->save();
        // Refresh updated_at
//        $model->content->refresh();

        return \humhub\modules\stream\actions\Stream::getContentResultEntry($model->content);
    }

    /**
     * Confirm a Message
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
            throw new HttpException(401, Yii::t('AnnouncementsModule.base', 'Message not found!'));
        }

        $query = User::find();
        $query->leftJoin('announcement_user', 'announcement_user.user_id=user.id');
        $query->andWhere(['announcement_user.announcement_id' => $announcement->id]);
        $query->andWhere(['announcement_user.confirmed' => true]);
//        $query->orderBy('announcement_user.created_at DESC');

        $title = Yii::t('AnnouncementsModule.base', "Users read: <strong>{title}</strong>", ['{title}' => Html::encode($announcement->title)]);

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
            throw new HttpException(401, Yii::t('AnnouncementsModule.base', 'Announcement not found!'));
        }

        $query = User::find();
        $query->leftJoin('announcement_user', 'announcement_user.user_id=user.id');
        $query->andWhere(['announcement_user.announcement_id' => $announcement->id]);
        $query->andWhere(['announcement_user.confirmed' => false]);
//        $query->orderBy('announcement_user.created_at DESC');

        $title = Yii::t('AnnouncementsModule.base', "Users didn't read: <strong>{title}</strong>", ['{title}' => Html::encode($announcement->title)]);

        return $this->renderAjaxContent(UserListBox::widget(['query' => $query, 'title' => $title]));
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
            throw new HttpException(401, Yii::t('AnnouncementsModule.base', 'Could not load Announcement!'));
        }

        if (!$announcement->content->canRead()) {
            throw new HttpException(401, Yii::t('AnnouncementsModule.base', 'You have insufficient permissions to perform that operation!'));
        }

        return $announcement;
    }


}
