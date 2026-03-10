<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Student;

/**
 * StudentSearch represents the model behind the search form of `common\models\Student`.
 */
class StudentSearch extends Student
{
    public $fullName;
    public $has_contract;
    public $has_paid;
    public $date_from;
    public $date_to;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'branch_id', 'direction_id', 'edu_form_id', 'edu_type_id', 'course_id', 'consulting_id'], 'integer'],
            [['phone', 'passport_series', 'passport_number', 'pinfl', 'fullName', 'date_from', 'date_to'], 'safe'],
            [['status'], 'safe'], // safe for array input due to multi-select
            [['has_contract', 'has_paid'], 'boolean'],
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
        // Eager load everything to prevent N+1 query loops
        $query = Student::find()->with([
            'direction',
            'eduForm',
            'eduType',
            'consulting',
            'studentOferta'
        ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // Exact matches
        $query->andFilterWhere([
            self::tableName() . '.id' => $this->id,
            self::tableName() . '.branch_id' => $this->branch_id,
            self::tableName() . '.direction_id' => $this->direction_id,
            self::tableName() . '.edu_form_id' => $this->edu_form_id,
            self::tableName() . '.edu_type_id' => $this->edu_type_id,
            self::tableName() . '.consulting_id' => $this->consulting_id,
        ]);

        // Support array of statuses
        if (!empty($this->status)) {
            $query->andWhere(['in', self::tableName() . '.status', $this->status]);
        }

        // Like searches
        $query->andFilterWhere(['like', self::tableName() . '.phone', $this->phone])
            ->andFilterWhere(['like', self::tableName() . '.pinfl', $this->pinfl])
            ->andFilterWhere(['like', self::tableName() . '.passport_series', $this->passport_series])
            ->andFilterWhere(['like', self::tableName() . '.passport_number', $this->passport_number]);

        // Full name search logic combining strings
        if ($this->fullName) {
            $query->andWhere([
                'or',
                ['like', self::tableName() . '.first_name', $this->fullName],
                ['like', self::tableName() . '.last_name', $this->fullName],
                ['like', self::tableName() . '.middle_name', $this->fullName],
            ]);
        }

        // Date range query matching UNIX timestamps
        if ($this->date_from) {
            $query->andFilterWhere(['>=', self::tableName() . '.created_at', strtotime($this->date_from . ' 00:00:00')]);
        }
        if ($this->date_to) {
            $query->andFilterWhere(['<=', self::tableName() . '.created_at', strtotime($this->date_to . ' 23:59:59')]);
        }

        // Contract generation virtual checks
        if ($this->has_contract === '1') {
            $query->innerJoinWith('studentOferta o1')->where(['is not', 'o1.id', null]);
        } elseif ($this->has_contract === '0') {
            // Need a subquery for 'NOT EXISTS' logically
            $query->andWhere(['not exists', \common\models\StudentOferta::find()->where('student_oferta.student_id = student.id')]);
        }

        if ($this->has_paid === '1') {
            $query->innerJoinWith('studentOferta o2')->andWhere(['o2.payment_status' => \common\models\StudentOferta::PAYMENT_PAID]);
        }

        return $dataProvider;
    }

    /**
     * Map out labels natively to standard array formats for Kartik Select2 usage
     */
    public static function getStatusOptions()
    {
        return self::getStatusList();
    }
}
