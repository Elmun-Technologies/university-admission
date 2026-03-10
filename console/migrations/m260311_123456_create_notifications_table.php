<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%notifications}}`.
 */
class m260311_123456_create_notifications_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%notifications}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'type' => $this->string(50)->notNull(),
            'title' => $this->string(255)->notNull(),
            'message' => $this->text()->notNull(),
            'link' => $this->string(255)->null(),
            'is_read' => $this->tinyInteger()->notNull()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
        ]);

        // Add foreign key for table `user`
        $this->addForeignKey(
            'fk-notifications-user_id',
            '{{%notifications}}',
            'user_id',
            '{{%user}}',
            'id',
            'CASCADE'
        );

        // Add index for performance on fetching unread specifically
        $this->createIndex(
            'idx-notifications-user_id_is_read',
            '{{%notifications}}',
            ['user_id', 'is_read']
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-notifications-user_id', '{{%notifications}}');
        $this->dropIndex('idx-notifications-user_id_is_read', '{{%notifications}}');
        $this->dropTable('{{%notifications}}');
    }
}
