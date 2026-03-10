<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%student_notification_pref}}`.
 */
class m260311_041900_create_student_notif_pref extends Migration
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

        $this->createTable('{{%student_notification_pref}}', [
            'id' => $this->primaryKey(),
            'student_id' => $this->integer()->notNull(),
            'type' => $this->string(20)->notNull(), // sms, telegram, email
            'is_enabled' => $this->boolean()->defaultValue(true),
            'telegram_id' => $this->bigInteger()->null(),
            'telegram_code' => $this->string(10)->null(), // 4-digit verification code
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->createIndex('idx-notif_pref-student_id', '{{%student_notification_pref}}', 'student_id');
        $this->addForeignKey('fk-notif_pref-student_id', '{{%student_notification_pref}}', 'student_id', '{{%student}}', 'id', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%student_notification_pref}}');
    }
}
