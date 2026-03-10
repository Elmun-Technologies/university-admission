<?php

use yii\db\Migration;

/**
 * Class m260101_000040_create_payment_transaction_table
 */
class m260101_000040_create_payment_transaction_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%payment_transaction}}', [
            'id' => $this->primaryKey(),
            'student_id' => $this->integer()->notNull(),
            'amount' => $this->decimal(15, 2)->notNull(),
            'currency' => $this->string(3)->defaultValue('UZS'),
            'ext_id' => $this->string(255)->comment('External transaction ID from Payme'),
            'status' => $this->integer()->defaultValue(0),
            'payload' => $this->text()->comment('Full JSON-RPC request log'),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        $this->addForeignKey(
            'fk-payment_transaction-student_id',
            '{{%payment_transaction}}',
            'student_id',
            '{{%student}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-payment_transaction-student_id', '{{%payment_transaction}}');
        $this->dropTable('{{%payment_transaction}}');
    }
}
