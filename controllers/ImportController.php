<?php

namespace app\controllers;

use Yii;
use yii\helpers\Inflector;
use yii\web\UploadedFile;
use yii\web\NotFoundHttpException;

class ImportController extends AbstractController
{
    public function init()
    {
        set_time_limit(0);
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'access' => [
                'class' => 'yii\filters\AccessControl',
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['transaction_manage'],
                    ],
                ],
            ],
        ]);
    }

    public function actionImport($object, array $attributes = [])
    {
        $class = 'app\\models\\import\\' . Inflector::camelize($object);

        if (!class_exists($class)) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        $model = new $class;

        if ($attributes) { // Extra params
            $model->setAttributes($attributes, false);
        }

        if ($post = Yii::$app->request->post()) {
            $model->load($post);
            if ($file = UploadedFile::getInstance($model, 'upload')) {
                if ($imported = $model->import($file->tempName)) {
                    $this->notice(__('{num} items were imported.', ['num' => $imported]));
                }
            }
            return $this->redirect(['import', 'object' => $object]);
        }

        return $this->render('import', [
            'model' => $model
        ]);
    }

}
