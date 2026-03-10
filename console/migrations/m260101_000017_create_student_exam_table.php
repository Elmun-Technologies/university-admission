<?php

use yii\db\Migration;

/**
 * Class m260101_000017_create_student_exam_table
 */
class m260101_000017_create_student_exam_table extends Migration
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

        $this->createTable('{{%student_exam}}', [
            'id' => $this->primaryKey(),
            'student_id' => $this->integer()->notNull(),
            'exam_id' => $this->integer()->notNull(),
            'exam_date_id' => $this->integer()->notNull(),
            'started_at' => $this->integer()->null()->comment('Unix timestamp when exam started'),
            'finished_at' => $this->integer()->null()->comment('Unix timestamp when exam finished'),
            'score' => $this->integer()->null()->comment('Final score achieved'),
            'max_score' => $this->integer()->null()->comment('Maximum possible score for this exam'),
            'is_passed' => $this->tinyInteger()->null()->comment('1=passed, 0=failed, null=not finished'),
            'status' => $this->tinyInteger()->notNull()->defaultValue(0)->comment('0=scheduled, 1=in_progress, 2=finished, 3=cancelled'),
            'created_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->createIndex('idx-student_exam-student_id', '{{%student_exam}}', 'student_id');
        $this->addForeignKey('fk-student_exam-student_id', '{{%student_exam}}', 'student_id', '{{%student}}', 'id', 'CASCADE', 'CASCADE');

        $this->createIndex('idx-student_exam-exam_id', '{{%student_exam}}', 'exam_id');
        $this->addForeignKey('fk-student_exam-exam_id', '{{%student_exam}}', 'exam_id', '{{%exam}}', 'id', 'CASCADE', 'CASCADE');

        $this->createIndex('idx-student_exam-exam_date_id', '{{%student_exam}}', 'exam_date_id');
        $this->addForeignKey('fk-student_exam-exam_date_id', '{{%student_exam}}', 'exam_date_id', '{{%exam_date}}', 'id', 'CASCADE', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-student_exam-exam_date_id', '{{%student_exam}}');
        $this->dropIndex('idx-student_exam-exam_date_id', '{{%student_exam}}');

        $this->dropForeignKey('fk-student_exam-exam_id', '{{%student_exam}}');
        $this->dropIndex('idx-student_exam-exam_id', '{{%student_exam}}');

        $this->dropForeignKey('fk-student_exam-student_id', '{{%student_exam}}');
        $this->dropIndex('idx-student_exam-student_id', '{{%student_exam}}');

        $this->dropTable('{{%student_exam}}');
    }
}
