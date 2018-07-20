<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2017 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\announcements\controllers;

use Yii;
use yii\helpers\Url;
use humhub\modules\admin\permissions\ManageModules;
use humhub\modules\admin\components\Controller;
use humhub\modules\announcements\models\forms\EditForm;

/**
 * 
 */
class ConfigController extends Controller
{

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function getAccessRules()
    {
        return [['permissions' => ManageModules::class]];
    }

    public function actionIndex()
    {
        $model = new EditForm();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $this->view->saved();
            return $this->redirect(Url::toRoute('index'));
        }

        return $this->render('index', [
            'model' => $model
        ]);
    }
}
