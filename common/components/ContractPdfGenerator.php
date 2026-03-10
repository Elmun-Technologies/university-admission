<?php

namespace common\components;

use Yii;
use common\models\StudentOferta;
use Mpdf\Mpdf;

/**
 * ContractPdfGenerator utilizes internal mPDF bindings to stamp Cyrillic and UTF-8 templates accurately
 */
class ContractPdfGenerator
{
    /**
     * Evaluates model and writes physical PDF strictly
     * @param StudentOferta $oferta
     * @return string File Absolute Path
     */
    public function generate(StudentOferta $oferta)
    {
        ini_set("pcre.backtrack_limit", "5000000"); // Standard requirement for heavy HTML parses organically mapping in mPDF

        $student = $oferta->student;
        $direction = $student->direction;
        $branch = $student->branch;
        $eduForm = $student->eduForm;

        // Render View Template natively
        $html = Yii::$app->view->renderFile('@common/views/contract_template.html.php', [
            'oferta' => $oferta,
            'student' => $student,
            'direction' => $direction,
            'branch' => $branch,
            'eduForm' => $eduForm,
        ]);

        $mpdf = new Mpdf([
            'margin_left' => 15,
            'margin_right' => 15,
            'margin_top' => 20,
            'margin_bottom' => 20,
            'margin_header' => 10,
            'margin_footer' => 10,
            'default_font' => 'dejavusans' // Guaranteed UTF-8 support mapped internally
        ]);

        $mpdf->SetTitle('Shartnoma: ' . $oferta->contract_number);
        $mpdf->SetAuthor($branch->name_uz ?? 'University');

        // Footer injection natively
        $mpdf->SetFooter('|{PAGENO} bet|' . date('d.m.Y H:i'));

        // Protect Document minimally
        $mpdf->SetProtection(['print']);

        $mpdf->WriteHTML($html);

        $dir = Yii::getAlias('@runtime/contracts');
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $filePath = $dir . DIRECTORY_SEPARATOR . $oferta->contract_number . '.pdf';

        try {
            $mpdf->Output($filePath, \Mpdf\Output\Destination::FILE);
            return $filePath;
        } catch (\Exception $e) {
            Yii::error('mPDF Generation Error: ' . $e->getMessage());
            return false;
        }
    }
}
