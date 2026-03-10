<?php

use yii\db\Migration;

/**
 * Class m260101_000011_create_questions_table
 */
class m260101_000011_create_questions_table extends Migration
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

        $this->createTable('{{%questions}}', [
            'id' => $this->primaryKey(),
            'subject_id' => $this->integer()->notNull(),
            'question_text' => $this->text()->notNull(),
            'question_text_ru' => $this->text()->null(),
            'difficulty' => $this->tinyInteger()->notNull()->defaultValue(1)->comment('1=easy, 2=medium, 3=hard'),
            'status' => $this->tinyInteger()->notNull()->defaultValue(1),
            'created_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->createIndex(
            'idx-questions-subject_id',
            '{{%questions}}',
            'subject_id'
        );

        $this->addForeignKey(
            'fk-questions-subject_id',
            '{{%questions}}',
            'subject_id',
            '{{%subject}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey(
            'fk-questions-subject_id',
            '{{%questions}}'
        );

        $this->dropIndex(
            'idx-questions-subject_id',
            '{{%questions}}'
        );

        $this->dropTable('{{%questions}}');
    }
}
