<?php

namespace common\components;

/**
 * PaymentGateway Interface enforces methods all payment integrations (Click, Payme, Uzum) must follow
 */
interface PaymentGateway
{
    /**
     * Start transaction with external provider
     *
     * @param float $amount Total amount
     * @param int $contractId Primary ID of the StudentOferta
     * @param string $returnUrl Fully qualified callback URL
     * @return string Returns Redirect URL to gateway
     */
    public function initPayment($amount, $contractId, $returnUrl);

    /**
     * Webhook/Verification lookup
     *
     * @param string $transactionId External provider's token
     * @return bool True if successful status verified
     */
    public function verifyPayment($transactionId);
}
