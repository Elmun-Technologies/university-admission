<?php

namespace tests\unit\models;

use common\models\Student;
use common\models\Direction;

class StudentTest extends \Codeception\Test\Unit
{
    /**
     * @var \tests\UnitTester
     */
    protected $tester;

    protected function _before()
    {
        // Typically load fixtures here
    }

    public function testValidationRules()
    {
        $student = new Student();

        // Empty fields
        $this->assertFalse($student->validate(['first_name']));
        $this->assertFalse($student->validate(['phone']));
        $this->assertFalse($student->validate(['passport_series']));

        // Correct assignments
        $student->first_name = 'Eshmat';
        $student->last_name = 'Toshmatov';
        $student->phone = '+998901234567';
        $student->passport_series = 'AA';
        $student->passport_number = '1234567';
        $student->pinfl = '12345678901234';

        $this->assertTrue($student->validate(['first_name']));
        $this->assertTrue($student->validate(['phone']));
        $this->assertTrue($student->validate(['passport_series', 'passport_number']));
    }

    public function testGetFullName()
    {
        $student = new Student();
        $student->first_name = 'Ali';
        $student->last_name = 'Valiyev';
        $student->middle_name = 'Umar o\'g\'li';

        $this->assertEquals("Valiyev Ali Umar o'g'li", $student->getFullName());
    }

    public function testStatusProgression()
    {
        $student = new Student();
        $student->status = Student::STATUS_NEW;

        $this->assertFalse($student->canTakeExam(), 'New student cannot take exam');

        $student->status = Student::STATUS_ANKETA;
        $this->assertTrue($student->canTakeExam(), 'Anketa student can take exam');
    }
}
