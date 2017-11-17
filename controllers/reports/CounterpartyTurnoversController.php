<?php

namespace app\controllers\reports;

use app\controllers\AbstractController;
use app\controllers\traits\ExportReportTrait;
use app\models\export\reports\CounterpartyTurnoversReportExport;
use app\models\report\transactions\CounterpartyTurnoversReport;
use Yii;
use yii\filters\AccessControl;

class CounterpartyTurnoversController extends AbstractController
{
    use ExportReportTrait;

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
        $searchModel = new CounterpartyTurnoversReport;
        $data = $searchModel->report(Yii::$app->request->queryParams);

        return $this->render('view', [
            'searchModel' => $searchModel,
            'data' => $data
        ]);
    }

    /**
     * Export report
     */
    public function actionExport()
    {
        return $this->performExport(new CounterpartyTurnoversReportExport, new CounterpartyTurnoversReport);
    }

}
