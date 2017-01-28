<?php

namespace app\models\search;

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
