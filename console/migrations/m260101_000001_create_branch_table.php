<?php

use yii\db\Migration;

/**
 * Class m260101_000001_create_branch_table
 */
class m260101_000001_create_branch_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%branch}}', [
            'id' => $this->primaryKey(),
            'name_uz' => $this->string(255)->notNull(),
            'name_ru' => $this->string(255)->null(),
            'name_en' => $this->string(255)->null(),
            'address_uz' => $this->text()->null(),
            'address_ru' => $this->text()->null(),
            'tel1' => $this->string(20)->null(),
            'tel2' => $this->string(20)->null(),
            'telegram' => $this->string(100)->null(),
            'instagram' => $this->string(100)->null(),
            'rector_uz' => $this->string(255)->null(),
            'rector_ru' => $this->string(255)->null(),
            'logo' => $this->string(255)->null(),
            'cons_id' => $this->integer()->null(),
            'status' => $this->tinyInteger()->notNull()->defaultValue(1),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        // Creates an index for the 'cons_id' column
        $this->createIndex(
            'idx-branch-cons_id',
            '{{%branch}}',
            'cons_id'
        );

        // Foreign key creation for the 'consulting' table could be added here later,
        // once the consulting table is created, or we skip actual FK constraints
        // depending on the project's strategy.
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Drop the index first
        $this->dropIndex(
            'idx-branch-cons_id',
            '{{%branch}}'
        );

        $this->dropTable('{{%branch}}');
    }
}
