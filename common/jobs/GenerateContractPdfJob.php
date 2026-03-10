<?php

namespace common\jobs;

use Yii;
use yii\base\BaseObject;
use yii\queue\JobInterface;
use common\models\StudentOferta;
use common\components\ContractPdfGenerator;

/**
 * Job to generate contract PDF
 */
class GenerateContractPdfJob extends BaseObject implements JobInterface
{
    public $ofertaId;

    public function execute($queue)
    {
        $oferta = StudentOferta::findOne($this->ofertaId);
        if (!$oferta) {
            return false;
        }

        try {
            // Reusing the existing generator component
            $generator = new ContractPdfGenerator();
            $generator->generate($oferta);
            return true;
        } catch (\Exception $e) {
            Yii::error("Failed to generate PDF for oferta {$this->ofertaId}: " . $e->getMessage());
            return false;
        }
    }
}
