<?php

use yii\db\Migration;

/**
 * Class m260101_000021_create_consulting_branch_table
 */
class m260101_000021_create_consulting_branch_table extends Migration
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

        $this->createTable('{{%consulting_branch}}', [
            'id' => $this->primaryKey(),
            'consulting_id' => $this->integer()->notNull(),
            'name' => $this->string(255)->notNull(),
            'address' => $this->string(255)->null(),
            'phone' => $this->string(20)->null(),
            'status' => $this->tinyInteger()->notNull()->defaultValue(1)->comment('1=active, 0=inactive'),
            'created_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->createIndex(
            'idx-consulting_branch-consulting_id',
            '{{%consulting_branch}}',
            'consulting_id'
        );

        $this->addForeignKey(
            'fk-consulting_branch-consulting_id',
            '{{%consulting_branch}}',
            'consulting_id',
            '{{%consulting}}',
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
            'fk-consulting_branch-consulting_id',
            '{{%consulting_branch}}'
        );

        $this->dropIndex(
            'idx-consulting_branch-consulting_id',
            '{{%consulting_branch}}'
        );

        $this->dropTable('{{%consulting_branch}}');
    }
}
