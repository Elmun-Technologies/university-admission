<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%help}}`.
 */
class m260311_041800_create_help_table extends Migration
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

        $this->createTable('{{%help}}', [
            'id' => $this->primaryKey(),
            'category_uz' => $this->string(100)->notNull(),
            'category_ru' => $this->string(100)->notNull(),
            'question_uz' => $this->text()->notNull(),
            'question_ru' => $this->text()->notNull(),
            'answer_uz' => $this->text()->notNull(),
            'answer_ru' => $this->text()->notNull(),
            'sort_order' => $this->integer()->defaultValue(0),
            'is_active' => $this->boolean()->defaultValue(true),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->createIndex('idx-help-is_active', '{{%help}}', 'is_active');
        $this->createIndex('idx-help-sort_order', '{{%help}}', 'sort_order');

        // Seed with common FAQs
        $now = time();
        $this->batchInsert('{{%help}}', 
            ['category_uz', 'category_ru', 'question_uz', 'question_ru', 'answer_uz', 'answer_ru', 'sort_order', 'created_at', 'updated_at'], 
            [
                [
                    'Ro\'yxatdan o\'tish', 'Регистрация', 
                    'Qanday qilib ro\'yxatdan o\'taman?', 'Как зарегистрироваться?', 
                    'Bosh sahifadagi "Ro\'yxatdan o\'tish" tugmasini bosing va telefon raqamingiz orqali anketani to\'ldiring.', 'Нажмите кнопку "Регистрация" на главной странице и заполните анкету через ваш номер телефона.', 
                    1, $now, $now
                ],
                [
                    'Hujjatlar', 'Документы', 
                    'Qaysi hujjatlar talab qilinadi?', 'Какие документы требуются?', 
                    'Pasport ma\'lumotlari, JSHSHIR (PINFL) va 3x4 o\'lchamdagi rasm kerak bo\'ladi.', 'Потребуются паспортные данные, ПИНФЛ и фотография размером 3x4.', 
                    2, $now, $now
                ],
                [
                    'Imtihon', 'Экзамен', 
                    'Imtihon qachon bo\'ladi?', 'Когда будет экзамен?', 
                    'Hujjatlaringiz tasdiqlanganidan so\'ng, shaxsiy kabinetingizda imtihon sanasini tanlash imkoniyati paydo bo\'ladi.', 'После подтверждения ваших документов, в личном кабинете появится возможность выбрать дату экзамена.', 
                    3, $now, $now
                ],
                [
                    'To\'lov', 'Оплата', 
                    'Shartnoma pulini qanday to\'layman?', 'Как оплатить контракт?', 
                    'Shartnoma imzolanganidan so\'ng, ilova orqali (Payme, Click) yoki bank kvitansiyasi orqali to\'lov qilish mumkin.', 'После подписания договора оплату можно произвести через приложение (Payme, Click) или через банковскую квитанцию.', 
                    4, $now, $now
                ],
                [
                    'Texnik yordam', 'Техподдержка', 
                    'Parolimni unutib qo\'ysam nima qilishim kerak?', 'Что делать, если я забыл пароль?', 
                    'Login sahifasidagi "Parolni unutdingizmi?" tugmasini bosing yoki qo\'llab-quvvatlash xizmatiga murojaat qiling.', 'Нажмите кнопку "Забыли пароль?" на странице входа или обратитесь в службу поддержки.', 
                    5, $now, $now
                ],
                [
                    'Yo\'nalishlar', 'Направления', 
                    'Yo\'nalishni o\'zgartirsa bo\'ladimi?', 'Можно ли изменить направление?', 
                    'Hujjatlar tasdiqlanguniga qadar yo\'nalishni o\'zgartirishingiz mumkin. Shundan so\'ng operatorga murojaat qilish kerak.', 'Вы можете изменить направление до тех пор, пока документы не подтверждены. После этого необходимо обратиться к оператору.', 
                    6, $now, $now
                ],
                [
                    'Imtihon', 'Экзамен', 
                    'Imtihon natijalari qachon chiqadi?', 'Когда выйдут результаты экзамена?', 
                    'Imtihon tugashi bilan natijalar avtomatik ravishda e\'lon qilinadi.', 'Результаты будут объявлены автоматически сразу после окончания экзамена.', 
                    7, $now, $now
                ],
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%help}}');
    }
}
