<?php

namespace app\models\search;

use app\models\components\SearchTrait;
use app\models\Currency;
use yii\data\ActiveDataProvider;

/**
 * CurrencySearch represents the model behind the search form about `app\models\Currency`.
 */
class CurrencySearch extends Currency
{
    use SearchTrait;

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Currency::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => $this->getPaginationDefaults(),
            'sort' => [
                'defaultOrder' => [
                    'name' => SORT_ASC,
                ],
            ],
        ]);

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
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'code', $this->code])
            ->andFilterWhere(['like', 'symbol', $this->symbol]);

        return $dataProvider;
    }
}
