<?php

namespace app\models\export;

use Yii;
use app\models\search\TransactionReportSearch;

class TransactionReport extends AbstractExport
{

    public $report;
    public $timestamp;
    public $timestamp_to;

    protected $_columns = [];

    public function findData()
    {
        $searchModel = new TransactionReportSearch();

        $report = $searchModel->search([
            'report' => $this->report,
            'timestamp' => $this->timestamp,
            'timestamp_to' => $this->timestamp_to,
        ]);

        $this->_columns = $report->exportGetColumns();

        $data = $report->execute();

        return $report->exportPrepareData($data);
    }

    protected function getColumnsDirect()
    {
        return $this->_columns;
    }

}
