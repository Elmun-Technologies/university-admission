<?php

use yii\db\Migration;

/**
 * Class m260101_000007_create_direction_edu_form_table
 */
class m260101_000007_create_direction_edu_form_table extends Migration
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

        $this->createTable('{{%direction_edu_form}}', [
            'direction_id' => $this->integer()->notNull(),
            'edu_form_id' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->addPrimaryKey(
            'pk-direction_edu_form',
            '{{%direction_edu_form}}',
            ['direction_id', 'edu_form_id']
        );

        $this->createIndex(
            'idx-direction_edu_form-direction_id',
            '{{%direction_edu_form}}',
            'direction_id'
        );

        $this->createIndex(
            'idx-direction_edu_form-edu_form_id',
            '{{%direction_edu_form}}',
            'edu_form_id'
        );

        $this->addForeignKey(
            'fk-direction_edu_form-direction_id',
            '{{%direction_edu_form}}',
            'direction_id',
            '{{%direction}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-direction_edu_form-edu_form_id',
            '{{%direction_edu_form}}',
            'edu_form_id',
            '{{%edu_form}}',
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
            'fk-direction_edu_form-direction_id',
            '{{%direction_edu_form}}'
        );

        $this->dropForeignKey(
            'fk-direction_edu_form-edu_form_id',
            '{{%direction_edu_form}}'
        );

        $this->dropIndex(
            'idx-direction_edu_form-direction_id',
            '{{%direction_edu_form}}'
        );

        $this->dropIndex(
            'idx-direction_edu_form-edu_form_id',
            '{{%direction_edu_form}}'
        );

        $this->dropTable('{{%direction_edu_form}}');
    }
}
