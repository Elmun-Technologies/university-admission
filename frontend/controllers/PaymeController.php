<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use common\components\PaymeGateway;

/**
 * PaymeController receives JSON-RPC requests from Payme Checkout
 */
class PaymeController extends Controller
{
    public $enableCsrfValidation = false;

    public function actionEndpoint()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        // Basic Auth Verification
        $authHeader = Yii::$app->request->headers->get('Authorization');
        if (!$this->authenticate($authHeader)) {
            Yii::$app->response->statusCode = 401;
            return ['error' => ['code' => -32504, 'message' => 'Unauthorized']];
        }

        $request = json_decode(Yii::$app->request->getRawBody(), true);
        if (!$request) {
            return ['error' => ['code' => -32700, 'message' => 'Parse error']];
        }

        $gateway = new PaymeGateway();
        return $gateway->process($request);
    }

    protected function authenticate($header)
    {
        if (!$header || strpos($header, 'Basic ') !== 0) {
            return false;
        }

        $payload = base64_decode(substr($header, 6));
        $parts = explode(':', $payload);

        $merchantId = getenv('PAYME_MERCHANT_ID') ?: 'Paycom';
        $key = getenv('PAYME_SECRET_KEY') ?: 'secret';

        return ($parts[0] === $merchantId && ($parts[1] === $key));
    }
}
