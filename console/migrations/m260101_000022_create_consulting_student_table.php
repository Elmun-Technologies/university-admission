<?php

use yii\db\Migration;

/**
 * Class m260101_000022_create_consulting_student_table
 */
class m260101_000022_create_consulting_student_table extends Migration
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

        $this->createTable('{{%consulting_student}}', [
            'id' => $this->primaryKey(),
            'consulting_id' => $this->integer()->notNull(),
            'consulting_branch_id' => $this->integer()->null(),
            'student_id' => $this->integer()->notNull(),
            'registered_at' => $this->integer()->notNull(),
            'commission_amount' => $this->decimal(12, 2)->null()->comment('Calculated when student pays'),
            'commission_paid' => $this->tinyInteger()->notNull()->defaultValue(0)->comment('0=unpaid, 1=paid'),
        ], $tableOptions);

        // A student can only be brought by one consulting agency
        $this->createIndex(
            'idx-consulting_student-student_id',
            '{{%consulting_student}}',
            'student_id',
            true
        );

        $this->addForeignKey(
            'fk-consulting_student-student_id',
            '{{%consulting_student}}',
            'student_id',
            '{{%student}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->createIndex(
            'idx-consulting_student-consulting_id',
            '{{%consulting_student}}',
            'consulting_id'
        );

        $this->addForeignKey(
            'fk-consulting_student-consulting_id',
            '{{%consulting_student}}',
            'consulting_id',
            '{{%consulting}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->createIndex(
            'idx-consulting_student-consulting_branch_id',
            '{{%consulting_student}}',
            'consulting_branch_id'
        );

        $this->addForeignKey(
            'fk-consulting_student-consulting_branch_id',
            '{{%consulting_student}}',
            'consulting_branch_id',
            '{{%consulting_branch}}',
            'id',
            'SET NULL',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-consulting_student-consulting_branch_id', '{{%consulting_student}}');
        $this->dropIndex('idx-consulting_student-consulting_branch_id', '{{%consulting_student}}');

        $this->dropForeignKey('fk-consulting_student-consulting_id', '{{%consulting_student}}');
        $this->dropIndex('idx-consulting_student-consulting_id', '{{%consulting_student}}');

        $this->dropForeignKey('fk-consulting_student-student_id', '{{%consulting_student}}');
        $this->dropIndex('idx-consulting_student-student_id', '{{%consulting_student}}');

        $this->dropTable('{{%consulting_student}}');
    }
}
