<?php

namespace app\controllers;

use app\controllers\traits\ExportTrait;
use app\models\export\TransactionExport;
use Yii;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\Transaction;
use app\models\Account;
use app\models\FormTemplate;
use app\models\search\TransactionSearch;
use app\models\form\MoneyTransferForm;

class TransactionController extends AbstractController
{
    use ExportTrait;

    const BATCH_LIMIT = 500;

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
                    'recalculate-balance' => ['POST'],
                ],
            ],
            'access' => [
                'class' => 'yii\filters\AccessControl',
                'rules' => [
                    [
                        'allow' => true,
                        'verbs' => ['GET'],
                        'actions' => ['index', 'update', 'transfer', 'download'],
                        'roles' => ['transaction_view'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['export', 'export-download'],
                        'roles' => ['transaction_view'],
                    ],
                    [
                        'allow' => true,
                        'verbs' => ['POST'],
                        'actions' => ['update', 'transfer'],
                        'roles' => ['transaction_edit'],
                    ],
                    [
                        'allow' => true,
                        'verbs' => ['POST'],
                        'actions' => ['delete'],
                        'roles' => ['transaction_delete'],
                    ],
                    [
                        'allow' => true,
                        'roles' => ['transaction_manage'],
                    ],
                    [
                        'allow' => false,
                    ],
                ],
            ],
        ]);
    }

    /**
     * Lists all Transaction models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TransactionSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $templates = FormTemplate::find()
            ->where(['model' => 'Transaction'])
            ->orderBy(['template' => SORT_ASC])
            ->all();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'templates' => $templates,
            'totals' => $searchModel->getTotals($dataProvider),
        ]);
    }

    /**
     * Updates an existing Transaction model.
     * If update is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id
     * @param integer $form_template_id
     * @param null $copy_id
     * @param array $searchParams
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id = null, $form_template_id = null, $copy_id = null, array $searchParams = [])
    {
        $touch_account_ids = [];

        if ($id) {
            $model = $this->findModel($id, Transaction::className());
            $touch_account_ids[] = $model->account_id;
        } else {
            $model = new Transaction;
            if (!$model->getCanUpdate()) {
                throw new ForbiddenHttpException('Update time is expired.');
            }
        }

        if ($post = Yii::$app->request->post()) {
            if ($model->load($post) && $model->save()) {
                $touch_account_ids[] = $model->account_id;
                $this->recalculateBalance($touch_account_ids);
                $this->notice(__('Your changes have been saved successfully.'));
            } else {
                $this->notice($model->errors, 'error');
            }
            return $this->redirect(['index']);
        }

        if (!$id) { // Is New
            $model->validate(); // Fill default values
            $model->clearErrors();
            if ($form_template_id) {
                FormTemplate::loadTemplate($model, $form_template_id, ['timestamp', 'attachmentsUpload']);
            } elseif ($copy_id) {
                $copy_model = Transaction::findOne($copy_id);
                $data = $copy_model->attributes;
                unset($data['timestamp']);
                $model->load($data, '');
            } elseif ($searchParams) {
                $model->load($searchParams, '');
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Money transfer.
     *
     * @param null $id
     * @param null $copy_id
     * @param array $searchParams
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionTransfer($id = null, $copy_id = null, array $searchParams = [])
    {
        $touch_account_ids = [];

        if ($id) {
            $model = MoneyTransferForm::findOne($id);
            if (!$model) {
                throw new NotFoundHttpException('The requested page does not exist.');
            }

            $touch_account_ids[] = $model->account_id_from;
            $touch_account_ids[] = $model->account_id_to;
        } else {
            $model = new MoneyTransferForm;
        }

        if ($post = Yii::$app->request->post()) {
            if ($model->load($post) && $model->save()) {
                $touch_account_ids[] = $model->account_id_from;
                $touch_account_ids[] = $model->account_id_to;
                $this->recalculateBalance($touch_account_ids);
                $this->notice(__('Your changes have been saved successfully.'));
            } else {
                $this->notice($model->errors, 'error');
            }

            return $this->redirect(['index']);
        }

        if (!$id) { // Is New
            $model->validate(); // Fill default values
            $model->clearErrors();
            if ($copy_id) {
                $copy_model = MoneyTransferForm::findOne($copy_id);
                if (!$copy_model) {
                    throw new NotFoundHttpException('The requested page does not exist.');
                }
                $data = $copy_model->attributes;
                unset($data['timestamp'], $data['transaction']);
                $model->load($data, '');
            } elseif ($searchParams) {
                $model->load($searchParams, '');
            }
        }

        return $this->render('transfer', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Transaction model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param array|int $id
     * @return mixed
     */
    public function actionDelete(array $id)
    {
        $ok_message = false;
        $models = Transaction::find()->where(['id' => $id])->all();
        if ($models) {
            $account_ids = [];
            foreach ($models as $model) {
                $account_ids[] = $model->account_id;
                if ($model->related_id) {
                    $account_ids[] = $model->related->account_id;
                }
                $model->delete();
            }

            $this->recalculateBalance($account_ids);

            if (count($models) > 1) {
                $ok_message = __('Items have been deleted successfully.');
            } else {
                $ok_message = __('Item has been deleted successfully.');
            }
        }

        if ($ok_message) {
            Yii::$app->session->setFlash('success', $ok_message);
        }
        if ($referrer = Yii::$app->request->referrer) {
            return $this->redirect($referrer);
        }

        return $this->redirect(['index']);
    }

    /**
     * Direct download attachment
     * @param  integer $id    Model ID
     * @param  string  $field Field name
     */
    public function actionDownload($id, $field)
    {
        $this->download($this->findModel($id, Transaction::className())->getPath($field));
    }

    public function actionExport()
    {
        return $this->performExport(
            new TransactionExport,
            (new TransactionSearch)->search(Yii::$app->request->queryParams)
        );
    }

    /**
     * Recalculate balances of Transaction model.
     * @return \yii\web\Response 302
     * @throws BadRequestHttpException
     */
    public function actionRecalculateBalance()
    {
        $accountId = $this->getRequest('account_id');
        if (!$accountId) {
            throw new BadRequestHttpException('Account ID field is empty');
        }

        $this->recalculateBalance($accountId);
        return $this->redirect(['index']);
    }

    protected function recalculateBalance($account_id)
    {
        $accounts = Account::find()->where(['id' => $account_id])->permission()->all();
        foreach ($accounts as $account) {
            $account->recalculateTransactionBalances();
        }
    }

}
