<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\User;

/**
 * UserSearch represents the model behind the search form of `common\models\User`.
 */
class UserSearch extends User
{
    public $fullName;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'branch_id', 'status'], 'integer'],
            [['username', 'email', 'fullName'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = User::find()->where(['!=', 'status', User::STATUS_DELETED]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'branch_id' => $this->branch_id,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'email', $this->email]);

        if ($this->fullName) {
            $query->andWhere([
                'or',
                ['like', 'first_name', $this->fullName],
                ['like', 'last_name', $this->fullName],
                ['like', 'middle_name', $this->fullName],
            ]);
        }

        return $dataProvider;
    }
}
