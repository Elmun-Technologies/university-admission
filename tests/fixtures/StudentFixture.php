<?php

namespace tests\fixtures;

use yii\test\ActiveFixture;
use common\models\Student;

class StudentFixture extends ActiveFixture
{
    public $modelClass = Student::class;
    public $depends = [
        BranchFixture::class,
        DirectionFixture::class,
    ];
}
