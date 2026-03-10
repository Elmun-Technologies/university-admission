<?php

namespace common\components;

use Yii;
use common\models\Student;
use common\models\StudentOferta;

/**
 * AmoCrmSync maps student life-cycle events to AmoCRM actions
 */
class AmoCrmSync
{
    private $_client;

    public function __construct()
    {
        $this->_client = new AmoCrmClient();
        // Here we'd get credentials from branch settings in a real app
        $branch = \common\models\Branch::find()->one();
        $configs = json_decode($branch->config_data ?? '{}', true);

        $this->_client->connect(
            $configs['amocrm_domain'] ?? '',
            $configs['amocrm_api_token'] ?? ''
        );
    }

    public function syncNewStudent(Student $student)
    {
        $existing = $this->_client->findLeadByPhone($student->phone);
        if ($existing)
            return $existing['id'];

        $leadData = [
            'name' => 'Abiturient: ' . $student->getFullName(),
            'custom_fields_values' => [
                ['field_id' => 12345, 'values' => [['value' => $student->phone]]], // Phone field ID
                ['field_id' => 67890, 'values' => [['value' => $student->direction->name_uz ?? '']]],
            ]
        ];

        $response = $this->_client->createLead($leadData);
        return $response['_embedded']['leads'][0]['id'] ?? null;
    }

    public function syncStatusChange(Student $student, $newStatus)
    {
        $lead = $this->_client->findLeadByPhone($student->phone);
        if (!$lead)
            return false;

        // Map internal status to AmoCRM Pipeline Stages (simplified mapping)
        $stageId = 0;
        switch ($newStatus) {
            case Student::STATUS_NEW:
                $stageId = 11111;
                break;
            case Student::STATUS_ANKETA:
                $stageId = 22222;
                break;
            case Student::STATUS_EXAM_PASSED:
                $stageId = 33333;
                break;
            case Student::STATUS_PAID:
                $stageId = 44444;
                break;
        }

        if ($stageId) {
            return $this->_client->updateLeadStatus($lead['id'], $stageId);
        }
        return false;
    }

    public function syncPayment(StudentOferta $oferta)
    {
        $student = $oferta->student;
        $lead = $this->_client->findLeadByPhone($student->phone);
        if (!$lead)
            return false;

        return $this->_client->request("leads/{$lead['id']}", 'PATCH', [
            'price' => (int) $oferta->payment_amount,
            'status_id' => 142 // WON stage
        ]);
    }
}
