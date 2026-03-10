<?php

namespace tests\functional\backend;

use backend\tests\FunctionalTester;
use tests\factories\ExamFactory;
use common\models\Exam;

class ExamManagementCest
{
    public function _before(FunctionalTester $I)
    {
        $I->amLoggedInAs(1);
    }

    public function createExam(FunctionalTester $I)
    {
        $I->amOnPage(['exam/create']);
        $I->fillField('Exam[name_uz]', 'New Entrance Exam ' . date('Y'));
        $I->fillField('Exam[duration_minutes]', 90);
        $I->click('Saqlash / Сохранить');

        $I->see('Imtihon muvaffaqiyatli yaratildi');
        $I->see('New Entrance Exam ' . date('Y'), 'h1');
    }

    public function scheduleExamDate(FunctionalTester $I)
    {
        $exam = Exam::find()->one() ?: ExamFactory::create();

        $I->amOnPage(['exam-date/create', 'exam_id' => $exam->id]);
        $I->fillField('ExamDate[exam_date]', date('Y-m-d', strtotime('+7 days')));
        $I->fillField('ExamDate[start_time]', '09:00');
        $I->fillField('ExamDate[end_time]', '11:00');
        $I->fillField('ExamDate[max_participants]', 50);
        $I->click('Saqlash / Сохранить');

        $I->see('Imtihon sanasi muvaffaqiyatli rejalashtirildi');
    }
}
