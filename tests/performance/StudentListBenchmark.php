<?php

namespace tests\performance;

use Yii;
use common\models\Student;

class StudentListBenchmark
{
    public function run()
    {
        $startTime = microtime(true);

        // Simulate loading student list with 10k records (if they exist)
        $students = Student::find()->limit(10000)->all();

        $endTime = microtime(true);
        $duration = $endTime - $startTime;

        return [
            'total_students' => count($students),
            'loading_time_ms' => round($duration * 1000, 2),
            'status' => $duration < 2.0 ? 'PASS' : 'FAIL',
        ];
    }
}
