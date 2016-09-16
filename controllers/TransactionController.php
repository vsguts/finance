<?php

namespace app\controllers;

use Yii;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\Transaction;
use app\models\Account;
use app\models\FormTemplate;
use app\models\search\TransactionSearch;
use app\models\form\MoneyTransferForm;
use app\models\search\TransactionReportSearch;

class TransactionController extends AbstractController
{
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
        ]);
    }

    /**
     * Updates an existing Transaction model.
     * If update is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @param integer $form_template_id
     * @return mixed
     */
    public function actionUpdate($id = null, $form_template_id = null, $copy_id = null)
    {
        $touch_account_ids = [];

        if ($id) {
            $model = $this->findModel(Transaction::className(), $id);
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
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Money transfer.
     * @return mixed
     */
    public function actionTransfer($id = null, $copy_id = null)
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
        }

        return $this->render('transfer', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Transaction model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
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
        $this->download($this->findModel(Transaction::className(), $id)->getPath($field));
    }

    /**
     * Recalculate balances of Transaction model.
     * @param mixed $account_id Bank Account ID(s)
     * @return 302
     */
    public function actionRecalculateBalance(array $account_id)
    {
        $this->recalculateBalance($account_id);
        return $this->redirect(['index']);
    }

    /**
     * Transaction Reports
     * @return mixed
     */
    public function actionReport()
    {
        $searchModel = new TransactionReportSearch();

        $report = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('report', [
            'searchModel' => $searchModel,
            'data' => $report->execute(),
            'report' => $report,
            'report_id' => $searchModel->report,
            'reports' => $searchModel->getReports(),
        ]);
    }

    protected function recalculateBalance($account_id)
    {
        $accounts = Account::find()->where(['id' => $account_id])->all();
        
        foreach ($accounts as $account) {
            $balance = $account->init_balance;

            $query = Transaction::find()
                ->where([
                    'account_id' => $account->id
                ])
                ->orderBy([
                    'timestamp' => SORT_ASC,
                    'id' => SORT_ASC,
                ]);

            $num = function($val) {
                return round($val, 2);
            };

            foreach ($query->batch(self::BATCH_LIMIT) as $models) {
                foreach ($models as $model) {
                    $model->scenario = Transaction::SCENARIO_RECALCULATE;
                    
                    $current_opening_balance = $model->opening_balance;
                    $current_balance = $model->balance;
                    
                    $model->opening_balance = $balance;
                    $model->calculateBalance();
                    $balance = $model->balance;

                    if (
                        $num($current_opening_balance) != $num($model->opening_balance)
                        || $num($current_balance) != $num($model->balance)
                    ) {
                        $model->save();
                    }
                }
            }
        }
    }

}
