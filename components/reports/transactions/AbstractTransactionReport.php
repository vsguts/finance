<?php

namespace app\components\reports\transactions;

use app\models\ClassificationCategory;
use Yii;
use app\components\reports\ReportAbstract;
use app\models\Account;
use app\models\Classification;
use app\models\Counterparty;
use app\models\Transaction;

abstract class AbstractTransactionReport extends ReportAbstract
{

    protected $_accounts;

    protected $_classifications;

    protected $_classification_categories;

    protected $_counterparties;

    protected $_transactions;

    protected $_previous_transactions = [];


    /**
     * @return Account[]
     */
    protected function getAccounts()
    {
        if (!$this->_accounts) {
            $this->_accounts = Account::find()
                ->permission()
                ->joinWith(['currency'])
                // ->active()
                ->orderBy([
                    'name' => SORT_ASC,
                    'currency.code' => SORT_ASC,
                ])
                ->indexBy('id')
                ->all();
        }

        return $this->_accounts;
    }

    /**
     * @return Classification[]
     */
    protected function getClassifications()
    {
        if (!$this->_classifications) {
            $this->_classifications = Classification::find()
                ->orderBy(['name' => SORT_ASC])
                ->indexBy('id')
                ->all();
        }

        return $this->_classifications;
    }

    /**
     * @return ClassificationCategory[]
     */
    protected function getClassificationCategories()
    {
        if (!$this->_classification_categories) {
            $this->_classification_categories = ClassificationCategory::find()
                ->orderBy(['name' => SORT_ASC])
                ->indexBy('id')
                ->all();
        }

        return $this->_classification_categories;
    }

    /**
     * @return Counterparty[]
     */
    protected function getCounterparties()
    {
        if (!$this->_counterparties) {
            $this->_counterparties = Counterparty::find()
                ->orderBy(['name' => SORT_ASC])
                ->indexBy('id')
                ->all();
        }

        return $this->_counterparties;
    }

    /**
     * @return Transaction[]
     */
    protected function getTransactions()
    {
        if (!$this->_transactions) {
            $this->getAccounts();
            $this->_transactions = Transaction::find()
                ->joinWith('account')
                ->where([
                    'and',
                    ['account_id' => array_keys($this->_accounts)],
                    ['>=', 'timestamp', $this->timestamp],
                    ['<=', 'timestamp', $this->timestamp_to + SECONDS_IN_DAY - 1],
                ])
                ->orderBy([
                    'timestamp' => SORT_ASC,
                    'id' => SORT_ASC,
                ])
                ->all();
        }

        return $this->_transactions;
    }

    /**
     * @return Transaction
     */
    protected function getAccountPreviousTransaction($account_id)
    {
        if (!array_key_exists($account_id, $this->_previous_transactions)) {
            $this->_previous_transactions[$account_id] = Transaction::find()
                ->where([
                    'and',
                    ['account_id' => $account_id],
                    ['<', 'timestamp', $this->timestamp],
                ])
                ->orderBy([
                    'timestamp' => SORT_DESC,
                    'id' => SORT_DESC,
                ])
                ->limit(1)
                ->one();
        }

        return $this->_previous_transactions[$account_id];
    }

}
