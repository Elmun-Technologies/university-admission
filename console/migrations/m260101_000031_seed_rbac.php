<?php

use yii\db\Migration;

/**
 * Class m260101_000031_seed_rbac
 */
class m260101_000031_seed_rbac extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        // Ensure we don't duplicate on migrations
        $auth->removeAll();

        // ------------------------------
        // CREATE PERMISSIONS
        // ------------------------------

        // Student permissions
        $viewStudent = $auth->createPermission('viewStudent');
        $viewStudent->description = 'View applications';
        $auth->add($viewStudent);

        $createStudent = $auth->createPermission('createStudent');
        $createStudent->description = 'Register new appplicants';
        $auth->add($createStudent);

        $updateStudent = $auth->createPermission('updateStudent');
        $updateStudent->description = 'Edit applicant details';
        $auth->add($updateStudent);

        $deleteStudent = $auth->createPermission('deleteStudent');
        $deleteStudent->description = 'Delete applicants';
        $auth->add($deleteStudent);

        $exportStudents = $auth->createPermission('exportStudents');
        $exportStudents->description = 'Export applicant data Excel/CSV';
        $auth->add($exportStudents);

        $changeStudentStatus = $auth->createPermission('changeStudentStatus');
        $changeStudentStatus->description = 'Move applicants through flow states';
        $auth->add($changeStudentStatus);

        $viewStudentExam = $auth->createPermission('viewStudentExam');
        $viewStudentExam->description = 'View individual exam results globally';
        $auth->add($viewStudentExam);

        // Exam permissions
        $viewExam = $auth->createPermission('viewExam');
        $viewExam->description = 'View scheduled exams';
        $auth->add($viewExam);

        $createExam = $auth->createPermission('createExam');
        $createExam->description = 'Schedule a new exam instance';
        $auth->add($createExam);

        $updateExam = $auth->createPermission('updateExam');
        $updateExam->description = 'Modify an existing exam configuration';
        $auth->add($updateExam);

        $manageExamQuestions = $auth->createPermission('manageExamQuestions');
        $manageExamQuestions->description = 'Create, Edit, Delete subjects and questions';
        $auth->add($manageExamQuestions);

        $viewExamResults = $auth->createPermission('viewExamResults');
        $viewExamResults->description = 'View mass metrics across exam bounds';
        $auth->add($viewExamResults);

        // Contract permissions
        $viewContract = $auth->createPermission('viewContract');
        $viewContract->description = 'View student commercial agreements';
        $auth->add($viewContract);

        $createContract = $auth->createPermission('createContract');
        $createContract->description = 'Generate new PDF Oferta contracts';
        $auth->add($createContract);

        $updateContract = $auth->createPermission('updateContract');
        $updateContract->description = 'Update contract statuses and logs';
        $auth->add($updateContract);

        // Settings permissions
        $manageDirections = $auth->createPermission('manageDirections');
        $manageDirections->description = 'Setup degrees, courses, fees';
        $auth->add($manageDirections);

        $manageUsers = $auth->createPermission('manageUsers');
        $manageUsers->description = 'Add operational personnel';
        $auth->add($manageUsers);

        $manageSettings = $auth->createPermission('manageSettings');
        $manageSettings->description = 'Branch level settings (Telegram API etc)';
        $auth->add($manageSettings);

        $viewReports = $auth->createPermission('viewReports');
        $viewReports->description = 'Analytical dashboard overview';
        $auth->add($viewReports);

        $manageConsulting = $auth->createPermission('manageConsulting');
        $manageConsulting->description = 'Add/Remove regional agency partners';
        $auth->add($manageConsulting);

        // ------------------------------
        // CREATE ROLES & ASSIGN PERMISSIONS
        // ------------------------------

        // 1. Consulting Role
        $consulting = $auth->createRole('consulting');
        $consulting->description = 'Agency viewing their own students';
        $auth->add($consulting);
        $auth->addChild($consulting, $viewStudent);

        // 2. Operator Role
        $operator = $auth->createRole('operator');
        $operator->description = 'Call center / physical receptionist';
        $auth->add($operator);
        $auth->addChild($operator, $viewStudent);
        $auth->addChild($operator, $createStudent);
        $auth->addChild($operator, $viewExam);
        $auth->addChild($operator, $viewContract);
        $auth->addChild($operator, $viewReports);

        // 3. Admin Role
        $admin = $auth->createRole('admin');
        $admin->description = 'Branch Manager';
        $auth->add($admin);
        // Includes everything EXCEPT manageSettings and deleteStudent logically.
        $auth->addChild($admin, $operator);
        $auth->addChild($admin, $updateStudent);
        $auth->addChild($admin, $exportStudents);
        $auth->addChild($admin, $changeStudentStatus);
        $auth->addChild($admin, $viewStudentExam);
        $auth->addChild($admin, $createExam);
        $auth->addChild($admin, $updateExam);
        $auth->addChild($admin, $manageExamQuestions);
        $auth->addChild($admin, $viewExamResults);
        $auth->addChild($admin, $createContract);
        $auth->addChild($admin, $updateContract);
        $auth->addChild($admin, $manageDirections);
        $auth->addChild($admin, $manageUsers);
        $auth->addChild($admin, $manageConsulting);

        // 4. Super Admin Role
        $superAdmin = $auth->createRole('superAdmin');
        $superAdmin->description = 'Global System Admin (Cross-Branch)';
        $auth->add($superAdmin);
        // Inherits all admin abilities
        $auth->addChild($superAdmin, $admin);
        // Receives the remaining critical locks
        $auth->addChild($superAdmin, $manageSettings);
        $auth->addChild($superAdmin, $deleteStudent);

        // System user 1 exists physically based on later seeds
        $auth->assign($superAdmin, 1);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        Yii::$app->authManager->removeAll();
    }
}
