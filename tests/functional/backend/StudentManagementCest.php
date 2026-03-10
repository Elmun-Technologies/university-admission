<?php

namespace tests\functional\backend;

use backend\tests\FunctionalTester;
use tests\fixtures\StudentFixture;
use tests\factories\StudentFactory;
use common\models\Student;

class StudentManagementCest
{
    public function _before(FunctionalTester $I)
    {
        $I->amLoggedInAs(1); // Assuming admin user ID 1 exists
    }

    public function seeStudentList(FunctionalTester $I)
    {
        $I->amOnPage(['student/index']);
        $I->see('Talabalar / Студенты', 'h1');
        $I->seeElement('table');
        $I->see('F.I.SH. / Ф.И.О.');
        $I->see('Holat / Статус');
    }

    public function filterStudentsByStatus(FunctionalTester $I)
    {
        $I->amOnPage(['student/index']);
        $I->selectOption('StudentSearch[status]', Student::STATUS_NEW);
        $I->click('Qidirish / Поиск');
        $I->seeInCurrentUrl('status=' . Student::STATUS_NEW);
    }

    public function changeStudentStatus(FunctionalTester $I)
    {
        // Get a student in NEW status
        $student = Student::findOne(['status' => Student::STATUS_NEW]);
        if (!$student) {
            $student = StudentFactory::create(['status' => Student::STATUS_NEW]);
        }

        $I->amOnPage(['student/view', 'id' => $student->id]);
        $I->see('Holatni o\'zgartirish / Изменить статус');

        $I->selectOption('StudentStatusForm[status]', Student::STATUS_REJECTED);
        $I->fillField('StudentStatusForm[note]', 'Test rejection note');
        $I->click('Saqlash / Сохранить');

        $I->see('Holat muvaffaqiyatli o\'zgartirildi');
        $I->see('Rad etilgan', '.badge');
    }
}
