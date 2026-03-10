<?php

namespace backend\components;

use Yii;
use yii\base\Component;
use PhpOffice\PhpSpreadsheet\IOFactory;
use common\models\Direction;
use common\models\Student;
use common\models\Question;
use common\models\QuestionOption;

/**
 * InitialDataImporter handles bulk loading from Excel templates
 */
class InitialDataImporter extends Component
{
    private $_errors = [];
    private $_successCount = 0;

    public function importDirections($filePath)
    {
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        $transaction = Yii::$app->db->beginTransaction();
        try {
            foreach ($rows as $index => $row) {
                if ($index === 0)
                    continue; // Skip header

                $model = new Direction();
                $model->name_uz = $row[0] ?? '';
                $model->name_ru = $row[1] ?? '';
                $model->code = $row[2] ?? '';
                $model->status = 1;

                if (!$model->save()) {
                    $this->_errors[] = "Qator {$index}: " . implode(', ', $model->getErrorSummary(true));
                } else {
                    $this->_successCount++;
                }
            }
            $transaction->commit();
            return true;
        } catch (\Exception $e) {
            $transaction->rollBack();
            $this->_errors[] = $e->getMessage();
            return false;
        }
    }

    public function importQuestions($filePath, $subjectId)
    {
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        foreach ($rows as $index => $row) {
            if ($index === 0)
                continue;

            $q = new Question();
            $q->subject_id = $subjectId;
            $q->text = $row[0] ?? '';
            $q->difficulty = $this->mapDifficulty($row[1] ?? 'easy');

            if ($q->save()) {
                // Import options
                for ($i = 0; $i < 4; $i++) {
                    $opt = new QuestionOption();
                    $opt->question_id = $q->id;
                    $opt->text = $row[2 + $i] ?? '';
                    $opt->is_correct = ($i == ($row[6] ?? 0)) ? 1 : 0;
                    $opt->save();
                }
                $this->_successCount++;
            } else {
                $this->_errors[] = "Question row {$index} failed.";
            }
        }
        return true;
    }

    protected function mapDifficulty($label)
    {
        switch (strtolower($label)) {
            case 'hard':
                return 3;
            case 'medium':
                return 2;
            default:
                return 1;
        }
    }

    public function getErrors()
    {
        return $this->_errors;
    }
    public function getSuccessCount()
    {
        return $this->_successCount;
    }
}
