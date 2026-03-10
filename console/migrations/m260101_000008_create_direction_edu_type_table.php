<?php

use yii\db\Migration;

/**
 * Class m260101_000008_create_direction_edu_type_table
 */
class m260101_000008_create_direction_edu_type_table extends Migration
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

        $this->createTable('{{%direction_edu_type}}', [
            'direction_id' => $this->integer()->notNull(),
            'edu_type_id' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->addPrimaryKey(
            'pk-direction_edu_type',
            '{{%direction_edu_type}}',
            ['direction_id', 'edu_type_id']
        );

        $this->createIndex(
            'idx-direction_edu_type-direction_id',
            '{{%direction_edu_type}}',
            'direction_id'
        );

        $this->createIndex(
            'idx-direction_edu_type-edu_type_id',
            '{{%direction_edu_type}}',
            'edu_type_id'
        );

        $this->addForeignKey(
            'fk-direction_edu_type-direction_id',
            '{{%direction_edu_type}}',
            'direction_id',
            '{{%direction}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-direction_edu_type-edu_type_id',
            '{{%direction_edu_type}}',
            'edu_type_id',
            '{{%edu_type}}',
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
            'fk-direction_edu_type-direction_id',
            '{{%direction_edu_type}}'
        );

        $this->dropForeignKey(
            'fk-direction_edu_type-edu_type_id',
            '{{%direction_edu_type}}'
        );

        $this->dropIndex(
            'idx-direction_edu_type-direction_id',
            '{{%direction_edu_type}}'
        );

        $this->dropIndex(
            'idx-direction_edu_type-edu_type_id',
            '{{%direction_edu_type}}'
        );

        $this->dropTable('{{%direction_edu_type}}');
    }
}
