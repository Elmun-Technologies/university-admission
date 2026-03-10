<?php

use yii\db\Migration;

/**
 * Class m260101_000018_create_student_exam_answer_table
 */
class m260101_000018_create_student_exam_answer_table extends Migration
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

        $this->createTable('{{%student_exam_answer}}', [
            'id' => $this->primaryKey(),
            'student_exam_id' => $this->integer()->notNull(),
            'question_id' => $this->integer()->notNull(),
            'selected_option_id' => $this->integer()->null(),
            'is_correct' => $this->tinyInteger()->null()->comment('1=correct, 0=incorrect, null=not answered'),
            'answered_at' => $this->integer()->null()->comment('Unix timestamp when answered'),
        ], $tableOptions);

        $this->createIndex('idx-student_exam_answer-student_exam_id', '{{%student_exam_answer}}', 'student_exam_id');
        $this->addForeignKey('fk-student_exam_answer-student_exam_id', '{{%student_exam_answer}}', 'student_exam_id', '{{%student_exam}}', 'id', 'CASCADE', 'CASCADE');

        $this->createIndex('idx-student_exam_answer-question_id', '{{%student_exam_answer}}', 'question_id');
        $this->addForeignKey('fk-student_exam_answer-question_id', '{{%student_exam_answer}}', 'question_id', '{{%questions}}', 'id', 'CASCADE', 'CASCADE');

        $this->createIndex('idx-student_exam_answer-selected_option_id', '{{%student_exam_answer}}', 'selected_option_id');
        $this->addForeignKey('fk-student_exam_answer-selected_option_id', '{{%student_exam_answer}}', 'selected_option_id', '{{%question_options}}', 'id', 'CASCADE', 'CASCADE');

        // Ensure a student cannot answer the same question multiple times in the same exam
        $this->createIndex(
            'idx-student_exam_answer-unique',
            '{{%student_exam_answer}}',
            ['student_exam_id', 'question_id'],
            true
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('idx-student_exam_answer-unique', '{{%student_exam_answer}}');

        $this->dropForeignKey('fk-student_exam_answer-selected_option_id', '{{%student_exam_answer}}');
        $this->dropIndex('idx-student_exam_answer-selected_option_id', '{{%student_exam_answer}}');

        $this->dropForeignKey('fk-student_exam_answer-question_id', '{{%student_exam_answer}}');
        $this->dropIndex('idx-student_exam_answer-question_id', '{{%student_exam_answer}}');

        $this->dropForeignKey('fk-student_exam_answer-student_exam_id', '{{%student_exam_answer}}');
        $this->dropIndex('idx-student_exam_answer-student_exam_id', '{{%student_exam_answer}}');

        $this->dropTable('{{%student_exam_answer}}');
    }
}
