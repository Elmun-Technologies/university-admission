<?php

use yii\db\Migration;

/**
 * Class m260101_000015_create_exam_subject_table
 */
class m260101_000015_create_exam_subject_table extends Migration
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

        $this->createTable('{{%exam_subject}}', [
            'id' => $this->primaryKey(),
            'exam_id' => $this->integer()->notNull(),
            'subject_id' => $this->integer()->notNull(),
            'questions_count' => $this->integer()->notNull()->defaultValue(10)->comment('Number of questions for this subject in the exam'),
        ], $tableOptions);

        $this->createIndex('idx-exam_subject-exam_id', '{{%exam_subject}}', 'exam_id');
        $this->addForeignKey('fk-exam_subject-exam_id', '{{%exam_subject}}', 'exam_id', '{{%exam}}', 'id', 'CASCADE', 'CASCADE');

        $this->createIndex('idx-exam_subject-subject_id', '{{%exam_subject}}', 'subject_id');
        $this->addForeignKey('fk-exam_subject-subject_id', '{{%exam_subject}}', 'subject_id', '{{%subject}}', 'id', 'CASCADE', 'CASCADE');

        // Prevent adding the same subject multiple times to the same exam
        $this->createIndex(
            'idx-exam_subject-unique',
            '{{%exam_subject}}',
            ['exam_id', 'subject_id'],
            true
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-exam_subject-subject_id', '{{%exam_subject}}');
        $this->dropIndex('idx-exam_subject-subject_id', '{{%exam_subject}}');

        $this->dropForeignKey('fk-exam_subject-exam_id', '{{%exam_subject}}');
        $this->dropIndex('idx-exam_subject-exam_id', '{{%exam_subject}}');

        $this->dropIndex('idx-exam_subject-unique', '{{%exam_subject}}');

        $this->dropTable('{{%exam_subject}}');
    }
}
