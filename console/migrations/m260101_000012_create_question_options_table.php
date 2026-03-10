<?php

use yii\db\Migration;

/**
 * Class m260101_000012_create_question_options_table
 */
class m260101_000012_create_question_options_table extends Migration
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

        $this->createTable('{{%question_options}}', [
            'id' => $this->primaryKey(),
            'question_id' => $this->integer()->notNull(),
            'option_text' => $this->text()->notNull(),
            'option_text_ru' => $this->text()->null(),
            'is_correct' => $this->tinyInteger()->notNull()->defaultValue(0)->comment('1=correct, 0=incorrect'),
        ], $tableOptions);

        $this->createIndex(
            'idx-question_options-question_id',
            '{{%question_options}}',
            'question_id'
        );

        $this->addForeignKey(
            'fk-question_options-question_id',
            '{{%question_options}}',
            'question_id',
            '{{%questions}}',
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
            'fk-question_options-question_id',
            '{{%question_options}}'
        );

        $this->dropIndex(
            'idx-question_options-question_id',
            '{{%question_options}}'
        );

        $this->dropTable('{{%question_options}}');
    }
}
