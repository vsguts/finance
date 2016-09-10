<?php

namespace app\models\import\transactions;

use Yii;
use app\models\BankTransaction;

class Inexfinance extends AbstractProvider
{
    
    public function getFormat()
    {
        return 'json';
    }

    public function prepareData($data)
    {
        pd(array_keys($data));

        // TODO
    }

    public function processData($data)
    {
    }

}
