<?php

use yii\db\Migration;

/**
 * Class m260101_000027_create_crm_push_table
 */
class m260101_000027_create_crm_push_table extends Migration
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

        $this->createTable('{{%crm_push}}', [
            'id' => $this->primaryKey(),
            'student_id' => $this->integer()->notNull(),
            'event_type' => $this->string(50)->notNull()->comment('registered, status_changed, exam_passed, paid'),
            'payload' => $this->text()->notNull()->comment('JSON data to send to CRM'),
            'status' => $this->tinyInteger()->notNull()->defaultValue(0)->comment('0=pending, 1=sent, 2=failed'),
            'attempts' => $this->tinyInteger()->notNull()->defaultValue(0)->comment('Retry counter'),
            'sent_at' => $this->integer()->null(),
            'created_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->createIndex(
            'idx-crm_push-student_id',
            '{{%crm_push}}',
            'student_id'
        );

        $this->addForeignKey(
            'fk-crm_push-student_id',
            '{{%crm_push}}',
            'student_id',
            '{{%student}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        // Index for querying pending events quickly
        $this->createIndex(
            'idx-crm_push-status',
            '{{%crm_push}}',
            'status'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-crm_push-student_id', '{{%crm_push}}');
        $this->dropIndex('idx-crm_push-status', '{{%crm_push}}');
        $this->dropIndex('idx-crm_push-student_id', '{{%crm_push}}');
        $this->dropTable('{{%crm_push}}');
    }
}
