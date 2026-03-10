<?php

namespace common\components;

use Yii;
use yii\base\Component;
use yii\helpers\Json;

/**
 * AmoCrmClient handles raw requests to AmoCRM API
 */
class AmoCrmClient extends Component
{
    private $_domain;
    private $_token;

    public function connect($domain, $token)
    {
        $this->_domain = $domain;
        $this->_token = $token;
    }

    /**
     * Generic API request method
     */
    public function request($endpoint, $method = 'GET', $data = [])
    {
        $url = "https://{$this->_domain}.amocrm.ru/api/v4/{$endpoint}";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->_token,
            'Content-Type: application/json'
        ]);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, Json::encode($data));
        } elseif ($method === 'PATCH') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
            curl_setopt($ch, CURLOPT_POSTFIELDS, Json::encode($data));
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode >= 400) {
            Yii::error("AmoCRM API Error [{$httpCode}]: " . $response);
            return false;
        }

        return Json::decode($response);
    }

    public function createLead($studentData)
    {
        return $this->request('leads', 'POST', [$studentData]);
    }

    public function updateLeadStatus($leadId, $statusId)
    {
        return $this->request("leads/{$leadId}", 'PATCH', [
            'status_id' => (int) $statusId
        ]);
    }

    public function findLeadByPhone($phone)
    {
        // Search leads by custom field or query
        $result = $this->request("leads?query=" . urlencode($phone));
        if ($result && isset($result['_embedded']['leads'])) {
            return $result['_embedded']['leads'][0];
        }
        return null;
    }
}
