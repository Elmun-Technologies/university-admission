<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\StudentOferta;

/**
 * StudentOfertaSearch represents the model behind the search form of `common\models\StudentOferta`.
 */
class StudentOfertaSearch extends StudentOferta
{
    public $branch_id;
    public $direction_id;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'student_id', 'payment_status', 'branch_id', 'direction_id'], 'integer'],
            [['contract_number', 'payment_date'], 'safe'],
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
        $query = StudentOferta::find()->joinWith(['student']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            self::tableName() . '.id' => $this->id,
            self::tableName() . '.student_id' => $this->student_id,
            self::tableName() . '.payment_status' => $this->payment_status,
            self::tableName() . '.payment_date' => $this->payment_date,
        ]);

        $query->andFilterWhere(['like', self::tableName() . '.contract_number', $this->contract_number]);

        // Virtual filter scopes joining the Student table directly
        if ($this->branch_id) {
            $query->andFilterWhere(['student.branch_id' => $this->branch_id]);
        }

        if ($this->direction_id) {
            $query->andFilterWhere(['student.direction_id' => $this->direction_id]);
        }

        return $dataProvider;
    }
}
