<?php

use yii\db\Migration;

/**
 * Class m260101_000019_create_student_oferta_table
 */
class m260101_000019_create_student_oferta_table extends Migration
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

        $this->createTable('{{%student_oferta}}', [
            'id' => $this->primaryKey(),
            'student_id' => $this->integer()->notNull(),
            'contract_number' => $this->string(50)->notNull()->unique()->comment('Auto-generated contract number'),
            'signed_at' => $this->integer()->null()->comment('Unix timestamp when contract was signed'),
            'payment_amount' => $this->decimal(12, 2)->null()->comment('Amount to be paid'),
            'payment_status' => $this->tinyInteger()->notNull()->defaultValue(0)->comment('0=unpaid, 1=partial, 2=paid'),
            'payment_date' => $this->date()->null(),
            'contract_file' => $this->string(255)->null()->comment('Path to generated PDF contract file'),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->createIndex(
            'idx-student_oferta-student_id',
            '{{%student_oferta}}',
            'student_id',
            true // Unique constraint ensuring one contract per student
        );

        $this->addForeignKey(
            'fk-student_oferta-student_id',
            '{{%student_oferta}}',
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
        $this->dropForeignKey('fk-student_oferta-student_id', '{{%student_oferta}}');
        $this->dropIndex('idx-student_oferta-student_id', '{{%student_oferta}}');

        $this->dropTable('{{%student_oferta}}');
    }
}
