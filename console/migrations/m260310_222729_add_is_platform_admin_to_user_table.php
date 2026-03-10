<?php

use yii\db\Migration;

/**
 * Class m260310_222729_add_is_platform_admin_to_user_table
 */
class m260310_222729_add_is_platform_admin_to_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%user}}', 'is_platform_admin', $this->boolean()->notNull()->defaultValue(0)->after('status'));
        
        // Grant platform admin to the first user
        $this->update('{{%user}}', ['is_platform_admin' => 1], ['id' => 1]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%user}}', 'is_platform_admin');
    }
}
