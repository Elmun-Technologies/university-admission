<?php

use yii\db\Migration;

/**
 * Class m260101_000005_seed_dictionaries
 */
class m260101_000005_seed_dictionaries extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // 1. Seed edu_form table
        $this->batchInsert('{{%edu_form}}', ['id', 'name_uz', 'status', 'sort_order'], [
            [1, 'Kunduzgi', 1, 1],
            [2, 'Sirtqi', 1, 2],
            [3, 'Kechki', 1, 3],
        ]);

        // 2. Seed edu_type table
        $this->batchInsert('{{%edu_type}}', ['id', 'name_uz', 'status', 'sort_order'], [
            [1, 'Qabul', 1, 1],
            [2, 'O\'qishni ko\'chirish', 1, 2],
            [3, 'UZBMB natija', 1, 3],
            [4, 'Magistratura', 1, 4],
        ]);

        // 3. Seed course table
        $this->batchInsert('{{%course}}', ['id', 'name_uz', 'status', 'sort_order'], [
            [1, '1-kurs', 1, 1],
            [2, '2-kurs', 1, 2],
            [3, '3-kurs', 1, 3],
            [4, '4-kurs', 1, 4],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Remove course data
        $this->delete('{{%course}}', ['id' => [1, 2, 3, 4]]);

        // Remove edu_type data
        $this->delete('{{%edu_type}}', ['id' => [1, 2, 3, 4]]);

        // Remove edu_form data
        $this->delete('{{%edu_form}}', ['id' => [1, 2, 3]]);
    }
}
