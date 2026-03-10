<?php

use yii\db\Migration;

/**
 * Class m260101_000016_create_student_table
 */
class m260101_000016_create_student_table extends Migration
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

        $this->createTable('{{%student}}', [
            // Internal IDs
            'id' => $this->primaryKey(),
            'branch_id' => $this->integer()->notNull(),

            // Personal Info
            'first_name' => $this->string(100)->notNull(),
            'last_name' => $this->string(100)->notNull(),
            'middle_name' => $this->string(100)->null(),
            'first_name_ru' => $this->string(100)->null(),
            'last_name_ru' => $this->string(100)->null(),
            'middle_name_ru' => $this->string(100)->null(),
            'birth_date' => $this->date()->null(),
            'gender' => $this->tinyInteger()->null()->comment('1=male, 2=female'),
            'phone' => $this->string(20)->notNull()->unique(),
            'phone2' => $this->string(20)->null(),
            'email' => $this->string(150)->null(),

            // Document info
            'passport_series' => $this->string(10)->null(),
            'passport_number' => $this->string(15)->null(),
            'passport_given_by' => $this->string(255)->null(),
            'passport_given_date' => $this->date()->null(),
            'pinfl' => $this->string(20)->null()->unique(),

            // Address info
            'region_id' => $this->integer()->null(),
            'district_id' => $this->integer()->null(),
            'address' => $this->text()->null(),

            // Academic info
            'direction_id' => $this->integer()->null(),
            'edu_form_id' => $this->integer()->null(),
            'edu_type_id' => $this->integer()->null(),
            'course_id' => $this->integer()->null(),
            'consulting_id' => $this->integer()->null()->comment('FK to consulting table if came via agency'),

            // Status & Tracking
            'status' => $this->tinyInteger()->notNull()->defaultValue(0)
                ->comment('0=new, 1=anketa_filled, 2=exam_scheduled, 3=exam_passed, 4=exam_failed, 5=contract_signed, 6=paid, 7=rejected'),
            'photo' => $this->string(255)->null(),
            'status_history' => $this->json()->null()->comment('JSON tracking of status changes with timestamps'),

            // Metadata
            'created_by' => $this->integer()->null()->comment('FK to user who registered this student, null if self-registered'),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        // Foreign key to branch
        $this->createIndex('idx-student-branch_id', '{{%student}}', 'branch_id');
        $this->addForeignKey('fk-student-branch_id', '{{%student}}', 'branch_id', '{{%branch}}', 'id', 'CASCADE', 'CASCADE');

        // Education Foreign Keys
        $this->createIndex('idx-student-direction_id', '{{%student}}', 'direction_id');
        $this->addForeignKey('fk-student-direction_id', '{{%student}}', 'direction_id', '{{%direction}}', 'id', 'SET NULL', 'CASCADE');

        $this->createIndex('idx-student-edu_form_id', '{{%student}}', 'edu_form_id');
        $this->addForeignKey('fk-student-edu_form_id', '{{%student}}', 'edu_form_id', '{{%edu_form}}', 'id', 'SET NULL', 'CASCADE');

        $this->createIndex('idx-student-edu_type_id', '{{%student}}', 'edu_type_id');
        $this->addForeignKey('fk-student-edu_type_id', '{{%student}}', 'edu_type_id', '{{%edu_type}}', 'id', 'SET NULL', 'CASCADE');

        $this->createIndex('idx-student-course_id', '{{%student}}', 'course_id');
        $this->addForeignKey('fk-student-course_id', '{{%student}}', 'course_id', '{{%course}}', 'id', 'SET NULL', 'CASCADE');

        // Note: Missing FK references for region_id, district_id, user(created_by) and consulting_id.
        // Assuming their migrations will be created later.
        $this->createIndex('idx-student-region_id', '{{%student}}', 'region_id');
        $this->createIndex('idx-student-district_id', '{{%student}}', 'district_id');
        $this->createIndex('idx-student-consulting_id', '{{%student}}', 'consulting_id');
        $this->createIndex('idx-student-created_by', '{{%student}}', 'created_by');

        // Search indexes
        $this->createIndex('idx-student-status', '{{%student}}', 'status');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-student-course_id', '{{%student}}');
        $this->dropIndex('idx-student-course_id', '{{%student}}');

        $this->dropForeignKey('fk-student-edu_type_id', '{{%student}}');
        $this->dropIndex('idx-student-edu_type_id', '{{%student}}');

        $this->dropForeignKey('fk-student-edu_form_id', '{{%student}}');
        $this->dropIndex('idx-student-edu_form_id', '{{%student}}');

        $this->dropForeignKey('fk-student-direction_id', '{{%student}}');
        $this->dropIndex('idx-student-direction_id', '{{%student}}');

        $this->dropForeignKey('fk-student-branch_id', '{{%student}}');
        $this->dropIndex('idx-student-branch_id', '{{%student}}');

        $this->dropIndex('idx-student-created_by', '{{%student}}');
        $this->dropIndex('idx-student-consulting_id', '{{%student}}');
        $this->dropIndex('idx-student-district_id', '{{%student}}');
        $this->dropIndex('idx-student-region_id', '{{%student}}');
        $this->dropIndex('idx-student-status', '{{%student}}');

        $this->dropTable('{{%student}}');
    }
}
