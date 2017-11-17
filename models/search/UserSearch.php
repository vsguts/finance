<?php

namespace app\models\search;

use app\helpers\StringHelper;
use app\helpers\Tools;
use app\models\components\SearchTrait;
use app\models\User;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Query;

/**
 * UserSearch represents the model behind the search form about `app\models\User`.
 */
class UserSearch extends User
{
    use SearchTrait;

    /**
     * @inheritdoc
     */
    public function attributes()
    {
        // add related fields to searchable attributes
        return array_merge(parent::attributes(), [
            // Range fields
            'user_role_id',
            'has_role',
            'permission',
        ]);
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'has_role' => __('Has role'),
            'permission' => __('Permission'),
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
        $query = User::find();

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
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'auth_key', $this->auth_key])
            ->andFilterWhere(['like', 'password_hash', $this->password_hash])
            ->andFilterWhere(['like', 'password_reset_token', $this->password_reset_token])
        ;

        if ($this->user_role_id) {
            $query->andWhere(['user.id' => Yii::$app->authManager->getUserIdsByRole($this->user_role_id)]);
        }

        if ($this->permission) {
            $authManager = Yii::$app->authManager;
            $roles = $authManager->getRolesByPermission($this->permission);
            $query->andWhere(['user.id' => $authManager->getUserIdsByRole($roles)]);
        }

        if (StringHelper::stringNotEmpty($this->has_role)) {
            $assigned = (new Query)
                ->select('user_id')
                ->distinct()
                ->from(Yii::$app->authManager->assignmentTable)
                ->column();
            if ($this->has_role) {
                $query->andWhere(['user.id' => $assigned]);
            } else {
                $query->andWhere(['not', ['user.id' => $assigned]]);
            }
        }

        return $dataProvider;
    }
}
