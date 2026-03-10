<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;
use yii\helpers\Console;
use tests\factories\StudentFactory;
use common\models\Direction;
use common\models\Branch;
use common\models\EduForm;
use common\models\EduType;
use common\models\Question;
use common\models\Subject;

class SeedController extends Controller
{
    public function actionStudents($count = 100)
    {
        $this->stdout("Seeding $count students...\n", Console::FG_YELLOW);

        $branch = Branch::find()->one();
        if (!$branch) {
            $this->stderr("No branch found!\n", Console::FG_RED);
            return;
        }

        \common\components\BranchScope::setBranchId($branch->id);

        for ($i = 0; $i < $count; $i++) {
            StudentFactory::create();
            if ($i % 10 == 0)
                $this->stdout(".");
        }

        $this->stdout("\nDone!\n", Console::FG_GREEN);
    }

    public function actionClear()
    {
        if ($this->confirm("Are you sure you want to clear all student and exam data?")) {
            Yii::$app->db->createCommand()->truncateTable('{{%student}}')->execute();
            Yii::$app->db->createCommand()->truncateTable('{{%student_exam}}')->execute();
            Yii::$app->db->createCommand()->truncateTable('{{%student_oferta}}')->execute();
            $this->stdout("Data cleared.\n", Console::FG_GREEN);
        }
    }
}
