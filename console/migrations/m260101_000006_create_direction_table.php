<?php

use yii\db\Migration;

/**
 * Class m260101_000006_create_direction_table
 */
class m260101_000006_create_direction_table extends Migration
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

        $this->createTable('{{%direction}}', [
            'id' => $this->primaryKey(),
            'branch_id' => $this->integer()->notNull(),
            'name_uz' => $this->string(255)->notNull(),
            'name_ru' => $this->string(255)->null(),
            'name_en' => $this->string(255)->null(),
            'code' => $this->string(20)->null(),
            'description_uz' => $this->text()->null(),
            'description_ru' => $this->text()->null(),
            'tuition_fee' => $this->decimal(12, 2)->null(),
            'duration_years' => $this->tinyInteger()->null(),
            'status' => $this->tinyInteger()->notNull()->defaultValue(1),
            'sort_order' => $this->integer()->notNull()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->createIndex(
            'idx-direction-branch_id',
            '{{%direction}}',
            'branch_id'
        );

        $this->addForeignKey(
            'fk-direction-branch_id',
            '{{%direction}}',
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
        $this->dropForeignKey(
            'fk-direction-branch_id',
            '{{%direction}}'
        );

        $this->dropIndex(
            'idx-direction-branch_id',
            '{{%direction}}'
        );

        $this->dropTable('{{%direction}}');
    }
}
