<?php

use yii\db\Migration;

/**
 * Class m260101_000020_create_consulting_table
 */
class m260101_000020_create_consulting_table extends Migration
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

        $this->createTable('{{%consulting}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull(),
            'phone' => $this->string(20)->null(),
            'email' => $this->string(150)->null(),
            'contact_person' => $this->string(255)->null(),
            'commission_percent' => $this->decimal(5, 2)->notNull()->defaultValue(0),
            'status' => $this->tinyInteger()->notNull()->defaultValue(1)->comment('1=active, 0=inactive'),
            'created_at' => $this->integer()->notNull(),
        ], $tableOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%consulting}}');
    }
}
