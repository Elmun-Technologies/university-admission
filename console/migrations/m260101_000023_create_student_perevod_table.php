<?php

use yii\db\Migration;

/**
 * Class m260101_000023_create_student_perevod_table
 */
class m260101_000023_create_student_perevod_table extends Migration
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

        $this->createTable('{{%student_perevod}}', [
            'id' => $this->primaryKey(),
            'student_id' => $this->integer()->notNull(),
            'from_university' => $this->string(255)->notNull(),
            'from_direction' => $this->string(255)->notNull(),
            'completed_years' => $this->tinyInteger()->notNull(),
            'transfer_documents' => $this->text()->null()->comment('JSON list of document paths'),
            'status' => $this->tinyInteger()->notNull()->defaultValue(0)->comment('0=pending, 1=approved, 2=rejected'),
            'notes' => $this->text()->null(),
        ], $tableOptions);

        $this->createIndex(
            'idx-student_perevod-student_id',
            '{{%student_perevod}}',
            'student_id'
        );

        $this->addForeignKey(
            'fk-student_perevod-student_id',
            '{{%student_perevod}}',
            'student_id',
            '{{%student}}',
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
            'fk-student_perevod-student_id',
            '{{%student_perevod}}'
        );

        $this->dropIndex(
            'idx-student_perevod-student_id',
            '{{%student_perevod}}'
        );

        $this->dropTable('{{%student_perevod}}');
    }
}
