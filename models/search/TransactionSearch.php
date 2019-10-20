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
            'classification_category_id',

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

        // grid filtering conditions
        $query->andFilterWhere([
            'transaction.id' => $this->id,
            'account_id' => $this->account_id,
            'classification_id' => $this->classification_id,
            'counterparty_id' => $this->counterparty_id ?: '',
            'classification.category_id' => $this->classification_category_id ?: '',
            'user_id' => $this->user_id,
            'account.currency_id' => $this->currency_id,
        ]);

        if ($this->counterparty_id === '0') {
            $query->andWhere('counterparty_id is NULL');
        }

        if ($this->classification_category_id === '0') {
            $query->andWhere('classification.category_id is NULL');
        }

        $query
            ->andFilterWhere(['like', 'transaction.description', $this->description])
        ;

        $this->addRangeCondition($query, 'timestamp');
        $this->addRangeCondition($query, 'inflow');
        $this->addRangeCondition($query, 'outflow');
        $this->addRangeCondition($query, 'balance');

        return $dataProvider;
    }

    public function getTotals(ActiveDataProvider $dataProvider)
    {
        /** @var Transaction[] $models */
        $models = $dataProvider->getModels();
        if (!$models) {
            return false;
        }

        $template = array_fill_keys(Yii::$app->currency->getCurrencyIds(), 0);

        $totals = [
            'inflow' => $template,
            'outflow' => $template,
        ];

        foreach ($models as $model) {
            foreach ($totals as $key => $_value) {
                $totals[$key][$model->account->currency_id] += $model->$key;
            }
        }

        return $totals;
    }

    public function getAttributesForCreation()
    {
        $attributes = array_intersect_key($this->attributes, array_flip([
            'account_id',
            'currency_id',
            'category_id',
            'classification_category_id',
            'timestamp',
            'timestamp_to',
            'description',
        ]));

        // Account fix
        if (isset($attributes['account_id']) && is_array($attributes['account_id'])) {
            $attributes['account_id'] = reset($attributes['account_id']);
        }

        // Timestamp fix
        if (!empty($attributes['timestamp_to'])) {
            $attributes['timestamp'] = $attributes['timestamp_to'];
        }

        return $attributes;
    }
}
