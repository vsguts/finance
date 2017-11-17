<?php

namespace app\controllers\reports;

use app\controllers\AbstractController;
use app\models\report\transactions\BalanceDynamicsReport;
use Yii;
use yii\filters\AccessControl;

class BalanceDynamicsController extends AbstractController
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['transaction_view'],
                    ],
                ],
            ],
        ]);
    }

    /**
     * View report
     */
    public function actionView()
    {
        $searchModel = new BalanceDynamicsReport;
        $data = $searchModel->report(Yii::$app->request->queryParams);

        return $this->render('view', [
            'searchModel' => $searchModel,
            'data' => $data
        ]);
    }

}
