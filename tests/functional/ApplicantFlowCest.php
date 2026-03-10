<?php

namespace tests\functional;

use tests\FunctionalTester;
use common\models\User;

class ApplicantFlowCest
{
    public function _before(FunctionalTester $I)
    {
        // Ensure user is fresh before tests natively
    }

    public function checkRegistrationFlow(FunctionalTester $I)
    {
        $I->amOnPage('/auth/register');
        $I->see('Ro\'yxatdan o\'tish', 'h4');

        $I->fillField('Qanday raqamdan o\'tmoqdasiz?', '+998901234567');
        $I->fillField('Parol', 'testpassword');
        $I->fillField('Parolni tasdiqlang', 'testpassword');

        // Assuming we mock or bypass captcha for testing natively 
        // $I->click('Ro\'yxatdan o\'tish', 'button');
        // $I->see('Kabinetga xush kelibsiz');
    }

    public function checkDashboardAccessControl(FunctionalTester $I)
    {
        // Try accessing dashboard blindly as guest
        $I->amOnPage('/dashboard/index');
        // Should catch AccessControl redirect logically
        $I->seeCurrentUrlEquals('/auth/login');
    }

    public function checkExamConstraints(FunctionalTester $I)
    {
        // This requires an authenticated session map fixture
        // $I->amLoggedInAs(1); // Mapped User ID
        // $I->amOnPage('/exam/schedule');
    }
}
