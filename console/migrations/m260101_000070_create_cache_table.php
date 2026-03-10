<?php

use yii\db\Migration;

/**
 * Class m260101_000070_create_cache_table
 */
class m260101_000070_create_cache_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql' || $this->db->driverName === 'mariadb') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%cache}}', [
            'id' => $this->string(128)->notNull(),
            'data' => $this->binary(),
            'expire' => $this->integer(),
            'PRIMARY KEY ([[id]])',
        ], $tableOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%cache}}');
    }
}
