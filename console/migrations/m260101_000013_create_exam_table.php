<?php

use yii\db\Migration;

/**
 * Class m260101_000013_create_exam_table
 */
class m260101_000013_create_exam_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%exam}}', [
            'id' => $this->primaryKey(),
            'branch_id' => $this->integer()->notNull(),
            'direction_id' => $this->integer()->notNull(),
            'edu_type_id' => $this->integer()->notNull(),
            'name_uz' => $this->string(255)->notNull(),
            'name_ru' => $this->string(255)->null(),
            'duration_minutes' => $this->integer()->notNull()->defaultValue(60),
            'questions_count' => $this->integer()->notNull()->defaultValue(30),
            'passing_score' => $this->integer()->notNull()->defaultValue(50),
            'status' => $this->tinyInteger()->notNull()->defaultValue(1)->comment('1=active, 0=inactive'),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->createIndex('idx-exam-branch_id', '{{%exam}}', 'branch_id');
        $this->addForeignKey('fk-exam-branch_id', '{{%exam}}', 'branch_id', '{{%branch}}', 'id', 'CASCADE', 'CASCADE');

        $this->createIndex('idx-exam-direction_id', '{{%exam}}', 'direction_id');
        $this->addForeignKey('fk-exam-direction_id', '{{%exam}}', 'direction_id', '{{%direction}}', 'id', 'CASCADE', 'CASCADE');

        $this->createIndex('idx-exam-edu_type_id', '{{%exam}}', 'edu_type_id');
        $this->addForeignKey('fk-exam-edu_type_id', '{{%exam}}', 'edu_type_id', '{{%edu_type}}', 'id', 'CASCADE', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-exam-edu_type_id', '{{%exam}}');
        $this->dropIndex('idx-exam-edu_type_id', '{{%exam}}');

        $this->dropForeignKey('fk-exam-direction_id', '{{%exam}}');
        $this->dropIndex('idx-exam-direction_id', '{{%exam}}');

        $this->dropForeignKey('fk-exam-branch_id', '{{%exam}}');
        $this->dropIndex('idx-exam-branch_id', '{{%exam}}');

        $this->dropTable('{{%exam}}');
    }
}
