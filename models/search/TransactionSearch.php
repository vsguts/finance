<?php

namespace app\models\search;

use app\models\components\SearchTrait;
use app\models\Transaction;
use Yii;
use yii\data\ActiveDataProvider;

/**
 * TransactionSearch represents the model behind the search form about `app\models\Transaction`.
 */
class TransactionSearch extends Transaction
{
    use SearchTrait;

    /**
     * @inheritdoc
     */
    public function attributes()
    {
        // add related fields to searchable attributes
        return array_merge(parent::attributes(), [

            // Relations
            'currency_id',
            'category_id',

            // Range fields
            'timestamp_to',
            'inflow_to',
            'outflow_to',
            'balance_to',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'currency_id' => __('Currency'),
            'category_id' => __('Counterparty category'),
            'counterparty_fill' => __('Counterparty fill'),
        ]);
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Transaction::find()->dependent();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => $this->getPaginationDefaults(),
            'sort' => [
                'defaultOrder' => [
                    'timestamp' => SORT_DESC,
                    'id' => SORT_DESC,
                ],
            ],
        ]);

        // Sorting
        $dataProvider->sort->attributes['account'] = [
            'asc' => ['account.bank' => SORT_ASC],
            'desc' => ['account.bank' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['classification'] = [
            'asc' => ['classification.name' => SORT_ASC],
            'desc' => ['classification.name' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['counterparty'] = [
            'asc' => ['counterparty.name' => SORT_ASC],
            'desc' => ['counterparty.name' => SORT_DESC],
        ];

        $params = $this->processParams($params);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $this->commonConditions($query);

        return $dataProvider;
    }

    protected function commonConditions($query)
    {
        if ($this->counterparty_id === '0') {
            $query->andWhere('counterparty_id is NULL');
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'transaction.id' => $this->id,
            'account_id' => $this->account_id,
            'classification_id' => $this->classification_id,
            'counterparty_id' => $this->counterparty_id ?: '',
            'user_id' => $this->user_id,
            'account.currency_id' => $this->currency_id,
        ]);

        $query
            ->andFilterWhere(['like', 'transaction.description', $this->description])
        ;

        $this->addRangeCondition($query, 'timestamp');
        $this->addRangeCondition($query, 'inflow');
        $this->addRangeCondition($query, 'outflow');
        $this->addRangeCondition($query, 'balance');
    }

    public function getTotals(ActiveDataProvider $dataProvider)
    {
        $models = $dataProvider->getModels();
        if (!$models) {
            return false;
        }

        $totals = [
            'inflow' => 0,
            'outflow' => 0,
        ];

        $prev_currency_id = null;
        foreach ($models as $model) {
            if ($prev_currency_id && $prev_currency_id != $model->account->currency_id) { // Currencies differs
                return false;
            }
            $prev_currency_id = $model->account->currency_id;
            foreach ($totals as $key => $_value) {
                $totals[$key] += $model->$key;
            }
        }

        foreach ($totals as $key => $value) {
            $totals[$key . 'Value'] = Yii::$app->formatter->asDecimal($value, 2) . ' ' . $model->account->currency->symbol;
        }
        return $totals;
    }

}
