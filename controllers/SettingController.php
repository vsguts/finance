<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use app\models\form\SettingsForm;

class SettingController extends AbstractController
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'verbs' => ['GET'],
                        'roles' => ['setting_view'],
                    ],
                    [
                        'allow' => true,
                        'verbs' => ['POST'],
                        'roles' => ['setting_manage'],
                    ],
                ],
            ],
        ]);
    }

    /**
     * Lists all State models.
     * @return mixed
     */
    public function actionIndex()
    {
        $model = new SettingsForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->saveSettings();
            Yii::$app->session->setFlash('success', __('Your changes have been saved successfully.'));
            return $this->redirect(['index']);
        } else {
            return $this->render('index', [
                'model' => $model,
            ]);
        }
    }

}
