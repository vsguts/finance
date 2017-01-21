<?php

namespace app\controllers;

use app\models\ClassificationCategory;
use app\models\search\ClassificationCategorySearch;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

/**
 * ClassificationCategoryController implements the CRUD actions for ClassificationCategory model.
 */
class ClassificationCategoryController extends AbstractController
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
            'access' => [
                'class' => AccessControl::class,
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
     * Lists all ClassificationCategory models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ClassificationCategorySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Updates an existing ClassificationCategory model.
     * If update is successful, the browser will be redirected to the 'update' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id = null)
    {
        if ($id) {
            $model = $this->findModel($id, ClassificationCategory::className());
        } else {
            $model = new ClassificationCategory();
        }

        if ($post = Yii::$app->request->post()) {
            if ($model->load($post) && $model->save()) {
                $this->notice(__('Your changes have been saved successfully.'));
            } else {
                $this->notice($model->errors, 'error');
            }
            return $this->redirect(['index']);
        }

        return $this->renderAjax('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing ClassificationCategory model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param array|int $id
     * @return mixed
     */
    public function actionDelete(array $id)
    {
        return $this->delete(ClassificationCategory::className(), $id);
    }

}
