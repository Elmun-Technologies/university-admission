<?php

use yii\db\Migration;

/**
 * Class m260310_221837_seed_initial_user
 */
class m260310_221837_seed_initial_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // 1. Create Initial Branch
        $this->insert('{{%branch}}', [
            'id' => 1,
            'name_uz' => 'Boshqaruv Markazi',
            'status' => 1,
            'created_at' => time(),
            'updated_at' => time(),
        ]);

        // 2. Create Super Admin User
        $this->insert('{{%user}}', [
            'id' => 1,
            'branch_id' => 1,
            'username' => 'admin',
            'email' => 'admin@beruniy.uz',
            'password_hash' => Yii::$app->security->generatePasswordHash('admin123'),
            'auth_key' => Yii::$app->security->generateRandomString(),
            'status' => 1,
            'created_at' => time(),
            'updated_at' => time(),
        ]);

        // RBAC assignment is done in m260101_000031_seed_rbac which we will run again or it will run because of fresh
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%user}}', ['id' => 1]);
        $this->delete('{{%branch}}', ['id' => 1]);
    }
}
