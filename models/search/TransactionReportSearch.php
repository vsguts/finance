<?php

namespace app\models\search;

use Yii;

class TransactionReportSearch extends AbstractReportSearch
{

    protected function getReportsNamespace()
    {
        return 'transactions';
    }

    protected function getDefaultReportName()
    {
        return 'account-turnovers';
    }

}
