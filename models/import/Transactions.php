<?php

namespace app\models\import;

use Yii;
use yii\helpers\Inflector;
use yii\base\Exception;
use app\models\Account;

class Transactions extends AbstractImport
{
    public $viewPath = 'transactions';

    public $account_id;

    public $processor;

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['account_id'], 'safe'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::rules(), [
            'account_id' => __('Account'),
        ]);
    }

    public function import($path)
    {
        $account = Account::findOne($this->account_id);

        if (!$account->import_processor) {
            throw new Exception('Import processor not found');
        }

        $class = 'app\models\import\transactions\\' . Inflector::camelize($account->import_processor);
        if (!class_exists($class)) {
            throw new Exception('Import processor not found');
        }

        $this->processor = Yii::createObject([
            'class' => $class,
            'account' => $account,
        ]);

        $result = parent::import($path);

        return $result;
    }

    public function getFormat()
    {
        return $this->processor->getFormat();
    }

    public function getEncoding()
    {
        return $this->processor->getEncoding();
    }

    public function getDelimiter()
    {
        return $this->processor->getDelimiter();
    }

    public function getHasCols()
    {
        return $this->processor->getHasCols();
    }

    protected function prepareData($data)
    {
        return $this->processor->prepareData($data);
    }


    public function getAccounts()
    {
        $accounts = Account::find()
            ->sorted()
            ->where([
                'and',
                ['not', ['import_processor' => NULL]],
                'import_processor != ""',
            ])->all();

        $result = [];
        foreach ($accounts as $account) {
            $result[$account->id] = $account->fullName;
        }
        return $result;
    }

    protected function processData($data)
    {
        return $this->processor->processData($data);
    }

}
