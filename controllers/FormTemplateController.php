<?php

namespace app\controllers;

use Yii;
use app\models\form\FormTemplateForm;

class FormTemplateController extends AbstractController
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'verbs' => [
                'class' => 'yii\filters\VerbFilter',
                'actions' => [
                    // 'delete' => ['POST'],
                ],
            ],
        ]);
    }

    public function actionCreate($model, $form_id)
    {
        $model_name = $model;
        $model = new FormTemplateForm();
        $model->model = $model_name;

        if ($post = Yii::$app->request->post()) {
            $model->load($post);
            $model->save();
            Yii::$app->session->setFlash('success', __('Your changes have been saved successfully.'));
            return $this->redirect(Yii::$app->getHomeUrl());
        }

        return $this->render('create', [
            'model' => $model,
            'form_id' => $form_id,
        ]);
    }

}
