<?php

use yii\db\Migration;

/**
 * Class m260101_000014_create_exam_date_table
 */
class m260101_000014_create_exam_date_table extends Migration
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

        $this->createTable('{{%exam_date}}', [
            'id' => $this->primaryKey(),
            'exam_id' => $this->integer()->notNull(),
            'exam_date' => $this->date()->notNull(),
            'start_time' => $this->time()->notNull(),
            'end_time' => $this->time()->notNull(),
            'max_participants' => $this->integer()->notNull()->defaultValue(100),
            'status' => $this->tinyInteger()->notNull()->defaultValue(1)->comment('0=cancelled, 1=scheduled, 2=ongoing, 3=finished'),
            'created_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->createIndex('idx-exam_date-exam_id', '{{%exam_date}}', 'exam_id');
        $this->addForeignKey('fk-exam_date-exam_id', '{{%exam_date}}', 'exam_id', '{{%exam}}', 'id', 'CASCADE', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-exam_date-exam_id', '{{%exam_date}}');
        $this->dropIndex('idx-exam_date-exam_id', '{{%exam_date}}');

        $this->dropTable('{{%exam_date}}');
    }
}
