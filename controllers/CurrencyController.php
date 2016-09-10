<?php

namespace app\controllers;

use Yii;
use app\models\Currency;
use app\models\search\CurrencySearch;
use yii\web\NotFoundHttpException;

/**
 * CurrencyController implements the CRUD actions for Currency model.
 */
class CurrencyController extends AbstractController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'verbs' => [
                'class' => 'yii\filters\VerbFilter',
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
                        'roles' => ['currency_view'],
                    ],
                    [
                        'allow' => true,
                        'verbs' => ['POST'],
                        'roles' => ['currency_manage'],
                    ],
                ],
            ],
        ]);
    }

    /**
     * Lists all Currency models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CurrencySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates or Updates an existing Currency model.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id = null)
    {
        if ($id) {
            $model = $this->findModel(Currency::className(), $id);
        } else {
            $model = new Currency;
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
     * Deletes an existing Currency model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete(array $id)
    {
        return $this->delete(Currency::className(), $id);
    }

}
