<?php

use yii\db\Migration;

/**
 * Class m260101_000025_init_rbac_tables
 */
class m260101_000025_init_rbac_tables extends Migration
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

        // auth_rule table
        $this->createTable('{{%auth_rule}}', [
            'name' => $this->string(64)->notNull(),
            'data' => $this->binary(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
            'PRIMARY KEY ([[name]])',
        ], $tableOptions);

        // auth_item table
        $this->createTable('{{%auth_item}}', [
            'name' => $this->string(64)->notNull(),
            'type' => $this->smallInteger()->notNull(),
            'description' => $this->text(),
            'rule_name' => $this->string(64),
            'data' => $this->binary(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
            'PRIMARY KEY ([[name]])',
            'FOREIGN KEY ([[rule_name]]) REFERENCES {{%auth_rule}} ([[name]]) ON DELETE SET NULL ON UPDATE CASCADE',
        ], $tableOptions);

        $this->createIndex('idx-auth_item-type', '{{%auth_item}}', 'type');

        // auth_item_child table
        $this->createTable('{{%auth_item_child}}', [
            'parent' => $this->string(64)->notNull(),
            'child' => $this->string(64)->notNull(),
            'PRIMARY KEY ([[parent]], [[child]])',
            'FOREIGN KEY ([[parent]]) REFERENCES {{%auth_item}} ([[name]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[child]]) REFERENCES {{%auth_item}} ([[name]]) ON DELETE CASCADE ON UPDATE CASCADE',
        ], $tableOptions);

        // auth_assignment table
        $this->createTable('{{%auth_assignment}}', [
            'item_name' => $this->string(64)->notNull(),
            'user_id' => $this->string(64)->notNull(),
            'created_at' => $this->integer(),
            'PRIMARY KEY ([[item_name]], [[user_id]])',
            'FOREIGN KEY ([[item_name]]) REFERENCES {{%auth_item}} ([[name]]) ON DELETE CASCADE ON UPDATE CASCADE',
        ], $tableOptions);

        $this->createIndex(
            'idx-auth_assignment-user_id',
            '{{%auth_assignment}}',
            'user_id'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%auth_assignment}}');
        $this->dropTable('{{%auth_item_child}}');
        $this->dropTable('{{%auth_item}}');
        $this->dropTable('{{%auth_rule}}');
    }
}
