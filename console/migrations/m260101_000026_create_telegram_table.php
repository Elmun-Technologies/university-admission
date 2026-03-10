<?php

use yii\db\Migration;

/**
 * Class m260101_000026_create_telegram_table
 */
class m260101_000026_create_telegram_table extends Migration
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

        $this->createTable('{{%telegram}}', [
            'id' => $this->primaryKey(),
            'branch_id' => $this->integer()->notNull()->unique(),
            'bot_token' => $this->string(255)->notNull(),
            'chat_id' => $this->string(50)->null(),
            'is_active' => $this->tinyInteger()->notNull()->defaultValue(1),
            'notify_new_student' => $this->tinyInteger()->notNull()->defaultValue(1),
            'notify_exam_result' => $this->tinyInteger()->notNull()->defaultValue(1),
            'notify_payment' => $this->tinyInteger()->notNull()->defaultValue(1),
            'created_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->addForeignKey(
            'fk-telegram-branch_id',
            '{{%telegram}}',
            'branch_id',
            '{{%branch}}',
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
        $this->dropForeignKey('fk-telegram-branch_id', '{{%telegram}}');
        $this->dropTable('{{%telegram}}');
    }
}
