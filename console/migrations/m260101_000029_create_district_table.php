<?php

use yii\db\Migration;

/**
 * Class m260101_000029_create_district_table
 */
class m260101_000029_create_district_table extends Migration
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

        $this->createTable('{{%district}}', [
            'id' => $this->primaryKey(),
            'region_id' => $this->integer()->notNull(),
            'name_uz' => $this->string(255)->notNull(),
            'name_ru' => $this->string(255)->null(),
            'name_en' => $this->string(255)->null(),
            'status' => $this->tinyInteger()->notNull()->defaultValue(1),
        ], $tableOptions);

        $this->createIndex(
            'idx-district-region_id',
            '{{%district}}',
            'region_id'
        );

        $this->addForeignKey(
            'fk-district-region_id',
            '{{%district}}',
            'region_id',
            '{{%region}}',
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
        $this->dropForeignKey('fk-district-region_id', '{{%district}}');
        $this->dropIndex('idx-district-region_id', '{{%district}}');
        $this->dropTable('{{%district}}');
    }
}
