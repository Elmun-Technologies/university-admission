<?php

namespace backend\components;

use PhpOffice\PhpSpreadsheet\IOFactory;
use common\models\Question;
use common\models\QuestionOption;
use Yii;

/**
 * QuestionImporter processes complex 12-column Excel sheets sequentially mapping subjects to questions and options natively.
 *
 * Expected Excel format:
 * | Question (uz) | Question (ru) | Option A (uz) | Option A (ru) | Option B (uz) | Option B (ru) |
 * | Option C (uz) | Option C (ru) | Option D (uz) | Option D (ru) | Correct (A/B/C/D) | Difficulty |
 */
class QuestionImporter
{
    /**
     * Reads file, returns array of question data
     */
    public function parseExcel($filePath, $subjectId)
    {
        try {
            $spreadsheet = IOFactory::load($filePath);
            $sheet = $spreadsheet->getActiveSheet();

            $data = [];
            // Skip header (Row 1), start from 2
            foreach ($sheet->getRowIterator(2) as $rowInfo) {
                $rowIndex = $rowInfo->getRowIndex();
                $cells = $sheet->rangeToArray('A' . $rowIndex . ':L' . $rowIndex, null, true, false)[0];

                // If the entire row is empty, break/skip logically
                if (empty(array_filter($cells))) {
                    continue;
                }

                $data[] = [
                    'row' => $rowIndex,
                    'subject_id' => $subjectId,
                    'text_uz' => trim($cells[0] ?? ''),
                    'text_ru' => trim($cells[1] ?? ''),
                    'options' => [
                        'A' => ['uz' => trim($cells[2] ?? ''), 'ru' => trim($cells[3] ?? '')],
                        'B' => ['uz' => trim($cells[4] ?? ''), 'ru' => trim($cells[5] ?? '')],
                        'C' => ['uz' => trim($cells[6] ?? ''), 'ru' => trim($cells[7] ?? '')],
                        'D' => ['uz' => trim($cells[8] ?? ''), 'ru' => trim($cells[9] ?? '')],
                    ],
                    'correct_key' => strtoupper(trim($cells[10] ?? '')),
                    'difficulty' => (int) ($cells[11] ?? Question::DIFFICULTY_NORMAL),
                ];
            }
            return $data;
        } catch (\Exception $e) {
            Yii::error("Excel parse failed: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Checks each row for required fields, returns validation errors per row
     */
    public function validate(array $questions)
    {
        $errors = [];
        foreach ($questions as $q) {
            $rowErrors = [];
            if (empty($q['text_uz']) && empty($q['text_ru'])) {
                $rowErrors[] = "Savol matni kiritilmagan";
            }

            $validKeys = ['A', 'B', 'C', 'D'];
            if (!in_array($q['correct_key'], $validKeys)) {
                $rowErrors[] = "To'g'ri javob belgisi (Correct) faqat A, B, C yoki D bo'lishi shart. Siz kiritdingiz: '{$q['correct_key']}'";
            }

            foreach ($validKeys as $k) {
                if (empty($q['options'][$k]['uz']) && empty($q['options'][$k]['ru'])) {
                    $rowErrors[] = "Javob varianti ($k) bo'sh";
                }
            }

            if (!empty($rowErrors)) {
                $errors[] = "Qator {$q['row']}: " . implode('; ', $rowErrors);
            }
        }
        return $errors;
    }

    /**
     * Saves to DB in a transaction mapping natively ensuring safe abort.
     */
    public function import(array $questions, $examId = null)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $successCount = 0;

        try {
            foreach ($questions as $qData) {
                $question = new Question();
                $question->subject_id = $qData['subject_id'];
                // Not mapped tightly to exams usually, usually scoped by subject pool, unless localized scoping required.
                $question->text_uz = $qData['text_uz'];
                $question->text_ru = $qData['text_ru'];
                $question->difficulty = $qData['difficulty'];
                $question->status = 1;

                if (!$question->save(false)) {
                    throw new \Exception("Qator {$qData['row']}: Savolni saqlash xatosi.");
                }

                foreach ($qData['options'] as $key => $optTexts) {
                    $option = new QuestionOption();
                    $option->question_id = $question->id;
                    $option->text_uz = $optTexts['uz'];
                    $option->text_ru = $optTexts['ru'];
                    $option->is_correct = ($key === $qData['correct_key']) ? 1 : 0;

                    if (!$option->save(false)) {
                        throw new \Exception("Qator {$qData['row']}: Javob variantini ($key) saqlash xatosi.");
                    }
                }
                $successCount++;
            }
            $transaction->commit();
            return ['success_count' => $successCount, 'error_count' => 0];
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::error("Import transaction failed: " . $e->getMessage());
            return ['success_count' => 0, 'error_count' => count($questions), 'errors' => [$e->getMessage()]];
        }
    }
}
