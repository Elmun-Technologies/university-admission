<?php

use yii\db\Migration;

/**
 * Class m260101_000050_database_optimization
 */
class m260101_000050_database_optimization extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // 1. Student List Optimization (Staff filters)
        $this->createIndex('idx-student-branch-status', '{{%student}}', ['branch_id', 'status']);
        $this->createIndex('idx-student-branch-direction-created', '{{%student}}', ['branch_id', 'direction_id', 'created_at']);

        // 2. Exam Question Fetching (Random selection per subject/difficulty)
        $this->createIndex('idx-question-subject-difficulty', '{{%questions}}', ['subject_id', 'difficulty']);

        // 3. Stats Cache Table
        $this->createTable('{{%stats_cache}}', [
            'id' => $this->primaryKey(),
            'branch_id' => $this->integer()->notNull(),
            'slug' => $this->string(50)->notNull()->comment('e.g. students_by_status'),
            'data_json' => $this->text()->notNull(),
            'computed_at' => $this->integer()->notNull(),
        ]);

        $this->createIndex('idx-stats_cache-branch-slug', '{{%stats_cache}}', ['branch_id', 'slug']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%stats_cache}}');
        $this->dropIndex('idx-question-subject-difficulty', '{{%questions}}');
        $this->dropIndex('idx-student-branch-direction-created', '{{%student}}');
        $this->dropIndex('idx-student-branch-status', '{{%student}}');
    }
}
