<?php

namespace common\components;

use common\models\StudentOferta;

/**
 * FakePaymentGateway mocks instant success transactions for development and sandbox testing
 */
class FakePaymentGateway implements PaymentGateway
{
    /**
     * Return instantly to a mock webhook/success route simulating a rapid redirect loop
     */
    public function initPayment($amount, $contractId, $returnUrl)
    {
        // Simulate a fake token purely identifying the contract internally
        $mockToken = base64_encode("mock_{$contractId}_" . time());

        // Append it manually to the test server webhook we will build
        $webhookUrl = \Yii::$app->urlManager->createAbsoluteUrl(['/payment/callback', 'token' => $mockToken, 'redir' => $returnUrl]);

        return $webhookUrl;
    }

    /**
     * Instantly decrypt fake payload and approve
     */
    public function verifyPayment($transactionId)
    {
        $decoded = base64_decode($transactionId);
        if (str_starts_with($decoded, 'mock_')) {
            $parts = explode('_', $decoded);
            $contractId = $parts[1] ?? null;

            if ($contractId) {
                // Instantly mark contract Paid natively
                $oferta = StudentOferta::findOne($contractId);
                if ($oferta) {
                    $oferta->payment_status = StudentOferta::PAYMENT_PAID;
                    $oferta->payment_date = date('Y-m-d H:i:s');
                    return $oferta->save(false);
                }
            }
        }
        return false;
    }
}
