<?php

namespace tests\fixtures;

use yii\test\ActiveFixture;
use common\models\Direction;

class DirectionFixture extends ActiveFixture
{
    public $modelClass = Direction::class;
    public $depends = [BranchFixture::class];
}
