<?php

namespace tests\functional\backend;

use backend\tests\FunctionalTester;
use tests\factories\StudentFactory;
use common\models\Student;
use common\models\StudentOferta;

class ContractManagementCest
{
    public function _before(FunctionalTester $I)
    {
        $I->amLoggedInAs(1);
    }

    public function generateContract(FunctionalTester $I)
    {
        // Student status should be eligible (e.g. PASSED)
        $student = StudentFactory::create(['status' => Student::STATUS_PASSED]);

        $I->amOnPage(['student/view', 'id' => $student->id]);
        $I->click('Shartnoma shakllantirish / Сформировать договор');

        $I->see('Shartnoma muvaffaqiyatli shakllantirildi');
        $I->see('BRN-' . date('Y'), 'table');
    }

    public function recordPayment(FunctionalTester $I)
    {
        $student = StudentFactory::create(['status' => Student::STATUS_CONTRACT]);
        $oferta = new StudentOferta();
        $oferta->student_id = $student->id;
        $oferta->contract_number = StudentOferta::generateContractNumber($student->branch_id);
        $oferta->save();

        $I->amOnPage(['student-oferta/update', 'id' => $oferta->id]);
        $I->fillField('StudentOferta[payment_amount]', 5000000);
        $I->selectOption('StudentOferta[payment_status]', StudentOferta::PAYMENT_PAID);
        $I->click('Saqlash / Сохранить');

        $I->see('To\'lov muvaffaqiyatli saqlandi');
        $I->see('To\'langan', '.badge');
    }
}
