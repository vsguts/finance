<?php

namespace app\controllers;

use Yii;
use app\models\Counterparty;
use app\models\CounterpartyCategory;
use app\models\search\CounterpartySearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CounterpartyController implements the CRUD actions for Counterparty model.
 */
class CounterpartyController extends AbstractController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'verbs' => [
                'class' => VerbFilter::className(),
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
                        'actions' => ['index', 'update', 'download'],
                        'roles' => ['counterparty_view'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['oauth'],
                        'roles' => ['counterparty_manage'],
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
     * Lists all Counterparty models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CounterpartySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates or Updates an existing Counterparty model.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id = null)
    {
        if ($id) {
            $model = $this->findModel($id, Counterparty::className());
        } else {
            $model = new Counterparty;
            $model->validate(); // Fill default values
            $model->clearErrors();
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
     * Deletes an existing Counterparty model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param array|int $id
     * @return mixed
     */
    public function actionDelete(array $id)
    {
        return $this->delete(Counterparty::className(), $id);
    }

    /**
     * Direct download attachment
     * @param  integer $id    Model ID
     * @param  string  $field Field name
     */
    public function actionDownload($id, $field)
    {
        $this->download($this->findModel($id, Counterparty::className())->getPath($field));
    }

    public function actionOauth($id = null, $code = null)
    {
        if ($id == null) {
            $id = Yii::$app->session['oauth_id'];
        }
        $model = $this->findModel($id, Counterparty::className());

        if ($model->getOauthIsConnected()) {
            $model->oauthDisconnect();
            $this->notice(__('Permissions of {object} were removed', ['object' => $model->name]));
            return $this->redirect(['index']);
        }

        if ($code) {
            $model->oauthSetCode($code);
            unset(Yii::$app->session['oauth_id']);
            $this->notice(__('Permissions to {object} were delegated', ['object' => $model->name]));
            return $this->redirect(['index']);
        }

        $url = $model->oauthConnect();

        Yii::$app->session['oauth_id'] = $id;

        return $this->redirect($url);
    }

}
