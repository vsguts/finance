<?php

namespace app\controllers;

use app\models\CounterpartyCategory;
use app\models\search\CounterpartyCategorySearch;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

/**
 * CounterpartyCategoryController implements the CRUD actions for CounterpartyCategory model.
 */
class CounterpartyCategoryController extends AbstractController
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'verbs' => [
                'class' => VerbFilter::class,
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
                        'roles' => ['counterparty_view'],
                    ],
                    [
                        'allow' => true,
                        'verbs' => ['POST'],
                        'roles' => ['counterparty_manage'],
                    ],
                ],
            ],
        ]);
    }

    /**
     * Lists all CounterpartyCategory models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CounterpartyCategorySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Updates an existing CounterpartyCategory model.
     * If update is successful, the browser will be redirected to the 'update' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id = null)
    {
        if ($id) {
            $model = $this->findModel($id, CounterpartyCategory::class);
        } else {
            $model = new CounterpartyCategory();
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
     * Deletes an existing CounterpartyCategory model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @return mixed
     */
    public function actionDelete()
    {
        $id = (array)$this->getRequest('id');

        return $this->delete(CounterpartyCategory::class, $id);
    }

}
