<?php

use yii\db\Migration;

/**
 * Class m260101_000009_create_subject_table
 */
class m260101_000009_create_subject_table extends Migration
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

        $this->createTable('{{%subject}}', [
            'id' => $this->primaryKey(),
            'name_uz' => $this->string(255)->notNull(),
            'name_ru' => $this->string(255)->null(),
            'name_en' => $this->string(255)->null(),
            'status' => $this->tinyInteger()->notNull()->defaultValue(1),
        ], $tableOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%subject}}');
    }
}
