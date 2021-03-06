<?php

namespace app\controllers;

use Yii;
use app\models\Classification;
use app\models\search\ClassificationSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ClassificationController implements the CRUD actions for Classification model.
 */
class ClassificationController extends AbstractController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'access' => [
                'class' => 'yii\filters\AccessControl',
                'rules' => [
                    [
                        'allow' => true,
                        'verbs' => ['GET'],
                        'actions' => ['index', 'update'],
                        'roles' => ['classification_view'],
                    ],
                    [
                        'allow' => true,
                        'verbs' => ['POST'],
                        'roles' => ['classification_manage'],
                    ],
                ],
            ],
        ]);
    }

    /**
     * Lists all Classification models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ClassificationSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates or Updates an existing Classification model.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id = null)
    {
        if ($id) {
            $model = $this->findModel($id, Classification::class);
        } else {
            $model = new Classification;
        }

        if ($post = Yii::$app->request->post()) {
            if ($model->load($post) && $model->save()) {
                $this->notice(__('Your changes have been saved successfully.'));
            } else {
                $this->notice($model->errors, 'error');
            }
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Classification model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @return mixed
     */
    public function actionDelete()
    {
        $id = (array)$this->getRequest('id');

        return $this->delete(Classification::class, $id);
    }

}
