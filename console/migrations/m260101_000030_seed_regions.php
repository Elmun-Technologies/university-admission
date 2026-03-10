<?php

use yii\db\Migration;

/**
 * Class m260101_000030_seed_regions
 */
class m260101_000030_seed_regions extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->batchInsert('{{%region}}', ['id', 'name_uz', 'name_ru', 'status'], [
            [1, 'Toshkent shahri', 'Город Ташкент', 1],
            [2, 'Toshkent viloyati', 'Ташкентская область', 1],
            [3, 'Andijon viloyati', 'Андижанская область', 1],
            [4, 'Buxoro viloyati', 'Бухарская область', 1],
            [5, 'Jizzax viloyati', 'Джизакская область', 1],
            [6, 'Qashqadaryo viloyati', 'Кашкадарьинская область', 1],
            [7, 'Navoiy viloyati', 'Навоийская область', 1],
            [8, 'Namangan viloyati', 'Наманганская область', 1],
            [9, 'Samarqand viloyati', 'Самаркандская область', 1],
            [10, 'Sirdaryo viloyati', 'Сырдарьинская область', 1],
            [11, 'Surxondaryo viloyati', 'Сурхандарьинская область', 1],
            [12, 'Farg\'ona viloyati', 'Ферганская область', 1],
            [13, 'Xorazm viloyati', 'Хорезмская область', 1],
            [14, 'Qoraqalpog\'iston Respublikasi', 'Республика Каракалпакстан', 1],
        ]);

        // Note: Missing actual district records logic as the prompt just asked to insert 
        // the 14 main regions + their official names! We can populate districts via CSV
        // in real-life as there are hundreds of them. 
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%region}}', ['id' => range(1, 14)]);
    }
}
