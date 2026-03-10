<?php

use yii\db\Migration;

/**
 * Class m260101_000010_create_direction_subject_table
 */
class m260101_000010_create_direction_subject_table extends Migration
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

        $this->createTable('{{%direction_subject}}', [
            'id' => $this->primaryKey(),
            'direction_id' => $this->integer()->notNull(),
            'subject_id' => $this->integer()->notNull(),
            'is_main' => $this->tinyInteger()->notNull()->defaultValue(0)->comment('1 if main subject, 0 otherwise'),
            'sort_order' => $this->integer()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->createIndex(
            'idx-direction_subject-direction_id',
            '{{%direction_subject}}',
            'direction_id'
        );

        $this->addForeignKey(
            'fk-direction_subject-direction_id',
            '{{%direction_subject}}',
            'direction_id',
            '{{%direction}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->createIndex(
            'idx-direction_subject-subject_id',
            '{{%direction_subject}}',
            'subject_id'
        );

        $this->addForeignKey(
            'fk-direction_subject-subject_id',
            '{{%direction_subject}}',
            'subject_id',
            '{{%subject}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        // Prevent duplicate subjects for the same direction
        $this->createIndex(
            'idx-direction_subject-unique',
            '{{%direction_subject}}',
            ['direction_id', 'subject_id'],
            true
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey(
            'fk-direction_subject-subject_id',
            '{{%direction_subject}}'
        );

        $this->dropIndex(
            'idx-direction_subject-subject_id',
            '{{%direction_subject}}'
        );

        $this->dropForeignKey(
            'fk-direction_subject-direction_id',
            '{{%direction_subject}}'
        );

        $this->dropIndex(
            'idx-direction_subject-direction_id',
            '{{%direction_subject}}'
        );

        $this->dropIndex(
            'idx-direction_subject-unique',
            '{{%direction_subject}}'
        );

        $this->dropTable('{{%direction_subject}}');
    }
}
