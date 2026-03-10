<?php

namespace backend\components;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Yii;

class StudentExporter
{
    /**
     * Generate an Excel file for the given ActiveDataProvider
     * @param \yii\data\ActiveDataProvider $dataProvider
     * @return string Path to the generated Excel file
     */
    public function export($dataProvider)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Abiturientlar Ro\'yxati');

        // Headers
        $headers = [
            'A1' => 'ID',
            'B1' => 'F.I.O.',
            'C1' => 'Telefon',
            'D1' => 'JSHSHIR (PINFL)',
            'E1' => 'Yo\'nalish',
            'F1' => 'Ta\'lim shakli',
            'G1' => 'Holat',
            'H1' => 'Imtihon bali',
        ];

        foreach ($headers as $cell => $val) {
            $sheet->setCellValue($cell, $val);
            $sheet->getStyle($cell)->getFont()->setBold(true);
        }

        $row = 2;
        $models = $dataProvider->getModels();
        foreach ($models as $student) {
            $sheet->setCellValueExplicit('A' . $row, str_pad($student->id, 6, '0', STR_PAD_LEFT), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue('B' . $row, $student->getFullName());
            $sheet->setCellValueExplicit('C' . $row, $student->phone, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('D' . $row, $student->pinfl, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue('E' . $row, $student->direction->name_uz ?? '');
            $sheet->setCellValue('F' . $row, $student->eduForm->name_uz ?? '');
            $sheet->setCellValue('G' . $row, $student->getStatusLabel());

            // Basic logic for exam score if available, otherwise 0
            $totalScore = 0;
            if ($student->studentExams) {
                foreach ($student->studentExams as $exam) {
                    $totalScore += $exam->score;
                }
            }
            $sheet->setCellValue('H' . $row, $totalScore);

            $row++;
        }

        // Auto-size columns
        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Ensure temp directory exists
        $dir = Yii::getAlias('@runtime/exports');
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $fileName = 'Export_' . time() . '.xlsx';
        $filePath = $dir . DIRECTORY_SEPARATOR . $fileName;

        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);

        return $filePath;
    }
}
