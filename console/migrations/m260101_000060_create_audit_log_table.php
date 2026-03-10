<?php

use yii\db\Migration;

/**
 * Class m260101_000060_create_audit_log_table
 */
class m260101_000060_create_audit_log_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%audit_log}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->null(),
            'branch_id' => $this->integer()->null(),
            'action' => $this->string(100)->notNull(),
            'entity_type' => $this->string(50)->notNull(),
            'entity_id' => $this->integer()->notNull(),
            'old_value' => $this->text()->null(),
            'new_value' => $this->text()->null(),
            'ip_address' => $this->string(45),
            'user_agent' => $this->string(255),
            'created_at' => $this->integer()->notNull(),
        ]);

        $this->createIndex('idx-audit_log-user', '{{%audit_log}}', 'user_id');
        $this->createIndex('idx-audit_log-entity', '{{%audit_log}}', ['entity_type', 'entity_id']);
        $this->createIndex('idx-audit_log-action', '{{%audit_log}}', 'action');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%audit_log}}');
    }
}
