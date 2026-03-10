<?php

namespace backend\components;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Yii;

/**
 * Handles huge payload exports of Student Models
 */
class StudentExporter
{
    public function export($dataProvider)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setTitle('Abiturientlar');

        // Header Format
        $branchName = Yii::$app->user->identity->branch->name_uz ?? 'Markaz';
        $sheet->setCellValue('A1', "{$branchName} - Abiturientlar Ro'yxati - Export sana: " . date('Y-m-d H:i'));
        $sheet->mergeCells('A1:L1');

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0d6efd']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ];

        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);

        $headers = [
            'A2' => '№',
            'B2' => 'F.I.SH',
            'C2' => 'Telefon',
            'D2' => 'Pasport',
            'E2' => 'Yo\'nalish',
            'F2' => 'Ta\'lim Shakli',
            'G2' => 'Ta\'lim Turi',
            'H2' => 'Imtihon Bali',
            'I2' => 'Shartnoma',
            'J2' => 'To\'lov Holati',
            'K2' => 'Reg. Sana',
            'L2' => 'Agentlik'
        ];

        foreach ($headers as $cell => $label) {
            $sheet->setCellValue($cell, $label);
        }
        $sheet->getStyle('A2:L2')->applyFromArray($headerStyle);
        $sheet->freezePane('A3');

        // Data Dump
        $row = 3;
        $models = $dataProvider->getModels();
        foreach ($models as $idx => $student) {
            $sheet->setCellValue('A' . $row, $idx + 1);
            $sheet->setCellValueExplicit('B' . $row, $student->getFullName(), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('C' . $row, $student->phone, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue('D' . $row, $student->passport_series . $student->passport_number);

            $sheet->setCellValueExplicit('E' . $row, $student->direction->name_uz ?? '', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue('F' . $row, $student->eduForm->name_uz ?? '');
            $sheet->setCellValue('G' . $row, $student->eduType->name_uz ?? '');

            // Score handling natively protecting from errors
            $scoreStr = '-';
            if ($exam = $student->getStudentExams()->one()) {
                $scoreStr = $exam->score . '%';
            }
            $sheet->setCellValue('H' . $row, $scoreStr);

            $sheet->setCellValue('I' . $row, $student->status >= \common\models\Student::STATUS_CONTRACT_SIGNED ? 'Tuzilgan' : 'Yo\'q');

            $paymentStr = '-';
            if ($oferta = $student->studentOferta) {
                if ($oferta->payment_status == \common\models\StudentOferta::PAYMENT_PAID) {
                    $paymentStr = 'To\'langan: ' . number_format($oferta->payment_amount, 0, '', ' ');
                } else {
                    $paymentStr = 'Kutilyapti';
                }
            }
            $sheet->setCellValue('J' . $row, $paymentStr);

            $sheet->setCellValue('K' . $row, date('d.m.Y', $student->created_at));
            $sheet->setCellValueExplicit('L' . $row, $student->consulting->name ?? '', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

            // Zebra striping dynamically
            if ($row % 2 == 0) {
                $sheet->getStyle("A{$row}:L{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F8F9FA');
            }
            $row++;
        }

        // Auto Sizes
        foreach (range('A', 'L') as $colId) {
            $sheet->getColumnDimension($colId)->setAutoSize(true);
        }

        // Temp export writing correctly mapping absolute bounds
        $filePath = Yii::getAlias('@runtime') . '/export_buffer_' . Yii::$app->security->generateRandomString(8) . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);

        return $filePath;
    }
}
