<?php

use yii\db\Migration;

/**
 * Class m260101_000024_create_user_table
 */
class m260101_000024_create_user_table extends Migration
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

        $this->createTable('{{%user}}', [
            'id' => $this->primaryKey(),
            'branch_id' => $this->integer()->notNull()->comment('Which university this user belongs to'),
            'username' => $this->string(100)->notNull()->unique(),
            'email' => $this->string(150)->notNull()->unique(),
            'password_hash' => $this->string(255)->notNull(),
            'auth_key' => $this->string(32)->notNull(),
            'password_reset_token' => $this->string(255)->unique(),
            'first_name' => $this->string(100)->null(),
            'last_name' => $this->string(100)->null(),
            'middle_name' => $this->string(100)->null(),
            'phone' => $this->string(20)->null(),
            'status' => $this->tinyInteger()->notNull()->defaultValue(1)->comment('0=inactive, 1=active, 9=deleted'),
            'last_login_at' => $this->integer()->null(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->createIndex(
            'idx-user-branch_id',
            '{{%user}}',
            'branch_id'
        );

        $this->addForeignKey(
            'fk-user-branch_id',
            '{{%user}}',
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
        $this->dropForeignKey('fk-user-branch_id', '{{%user}}');
        $this->dropIndex('idx-user-branch_id', '{{%user}}');
        $this->dropTable('{{%user}}');
    }
}
