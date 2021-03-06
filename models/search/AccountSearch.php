<?php

namespace app\models\search;

use app\models\Account;
use app\models\components\SearchTrait;
use yii\data\ActiveDataProvider;

/**
 * AccountSearch represents the model behind the search form about `app\models\Account`.
 */
class AccountSearch extends Account
{
    use SearchTrait;

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
        $query = Account::find()->dependent();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => $this->getPaginationDefaults(),
            'sort' => [
                'defaultOrder' => [
                    'status' => SORT_ASC,
                    'name' => SORT_ASC,
                    'currency' => SORT_ASC,
                ],
            ],
        ]);

        $dataProvider->sort->attributes['currency'] = [
            'asc' => ['currency.code' => SORT_ASC],
            'desc' => ['currency.code' => SORT_DESC],
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
            'id' => $this->id,
            'currency_id' => $this->currency_id,
            'status' => $this->status,
            'init_balance' => $this->init_balance,
        ]);

        $query
            ->andFilterWhere(['like', 'account.name', $this->name])
            ->andFilterWhere(['like', 'bank', $this->bank])
            ->andFilterWhere(['like', 'account_number', $this->account_number])
            ->andFilterWhere(['like', 'notes', $this->notes])
        ;

        return $dataProvider;
    }
}
