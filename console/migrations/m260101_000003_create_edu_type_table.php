<?php

use yii\db\Migration;

/**
 * Class m260101_000003_create_edu_type_table
 */
class m260101_000003_create_edu_type_table extends Migration
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

        $this->createTable('{{%edu_type}}', [
            'id' => $this->primaryKey(),
            'name_uz' => $this->string(255)->notNull(),
            'name_ru' => $this->string(255)->null(),
            'name_en' => $this->string(255)->null(),
            'status' => $this->tinyInteger()->notNull()->defaultValue(1),
            'sort_order' => $this->integer()->notNull()->defaultValue(0),
        ], $tableOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%edu_type}}');
    }
}
