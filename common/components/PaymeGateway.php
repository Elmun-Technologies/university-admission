<?php

namespace common\components;

use Yii;
use yii\base\Component;
use common\models\Student;
use yii\db\Query;

/**
 * PaymeGateway implements Uzb Payme JSON-RPC 2.0 protocol
 */
class PaymeGateway extends Component
{
    const ERROR_INVALID_AMOUNT = -31001;
    const ERROR_TRANSACTION_NOT_FOUND = -31003;
    const ERROR_ORDER_NOT_FOUND = -31050;
    const ERROR_ORDER_ALREADY_PAID = -31051;
    const ERROR_TRANSACTION_STATE_INVALID = -31052;

    public function process($request)
    {
        $method = $request['method'] ?? '';
        $params = $request['params'] ?? [];
        $id = $request['id'] ?? null;

        try {
            switch ($method) {
                case 'CheckPerformTransaction':
                    return $this->checkPerformTransaction($params, $id);
                case 'CreateTransaction':
                    return $this->createTransaction($params, $id);
                case 'PerformTransaction':
                    return $this->performTransaction($params, $id);
                case 'CancelTransaction':
                    return $this->cancelTransaction($params, $id);
                case 'CheckTransaction':
                    return $this->checkTransaction($params, $id);
                default:
                    return ['error' => ['code' => -32601, 'message' => 'Method not found'], 'id' => $id];
            }
        } catch (\Exception $e) {
            return ['error' => ['code' => -32400, 'message' => $e->getMessage()], 'id' => $id];
        }
    }

    protected function checkPerformTransaction($params, $id)
    {
        $orderId = $params['account']['student_id'] ?? null;
        $amount = $params['amount'] ?? 0;

        $student = Student::findOne($orderId);
        if (!$student) {
            return ['error' => ['code' => self::ERROR_ORDER_NOT_FOUND, 'message' => 'Order not found'], 'id' => $id];
        }

        // Check if already paid logic would go here

        return ['result' => ['allow' => true], 'id' => $id];
    }

    protected function createTransaction($params, $id)
    {
        $extId = $params['id'];
        $orderId = $params['account']['student_id'];
        $amount = $params['amount'] / 100; // Payme sends in tiyin

        // Check if exists
        $txn = (new Query())->from('payment_transaction')->where(['ext_id' => $extId])->one();
        if ($txn) {
            return [
                'result' => [
                    'create_time' => (int) $txn['created_at'] * 1000,
                    'transaction' => (string) $txn['id'],
                    'state' => 1
                ],
                'id' => $id
            ];
        }

        // Create new
        Yii::$app->db->createCommand()->insert('payment_transaction', [
            'student_id' => $orderId,
            'amount' => $amount,
            'ext_id' => $extId,
            'status' => 1, // State: Created
            'payload' => json_encode($params),
            'created_at' => time(),
            'updated_at' => time(),
        ])->execute();

        $newId = Yii::$app->db->getLastInsertID();

        return [
            'result' => [
                'create_time' => time() * 1000,
                'transaction' => (string) $newId,
                'state' => 1
            ],
            'id' => $id
        ];
    }

    protected function performTransaction($params, $id)
    {
        $extId = $params['id'];
        $txn = (new Query())->from('payment_transaction')->where(['ext_id' => $extId])->one();

        if (!$txn) {
            return ['error' => ['code' => self::ERROR_TRANSACTION_NOT_FOUND, 'message' => 'Transaction not found'], 'id' => $id];
        }

        if ($txn['status'] == 1) { // If state is created, mark as performed
            Yii::$app->db->createCommand()->update('payment_transaction', [
                'status' => 2, // State: Performed
                'updated_at' => time(),
            ], ['id' => $txn['id']])->execute();

            // Trigger actual payment logic in student model
            // ... (updating StudentOferta)
        }

        return [
            'result' => [
                'transaction' => (string) $txn['id'],
                'perform_time' => time() * 1000,
                'state' => 2
            ],
            'id' => $id
        ];
    }

    protected function cancelTransaction($params, $id)
    {
        // ... (Cancellation logic)
        return ['result' => ['state' => -1, 'cancel_time' => time() * 1000, 'transaction' => '1'], 'id' => $id];
    }

    protected function checkTransaction($params, $id)
    {
        $extId = $params['id'];
        $txn = (new Query())->from('payment_transaction')->where(['ext_id' => $extId])->one();

        if (!$txn) {
            return ['error' => ['code' => self::ERROR_TRANSACTION_NOT_FOUND], 'id' => $id];
        }

        return [
            'result' => [
                'create_time' => (int) $txn['created_at'] * 1000,
                'perform_time' => (int) $txn['updated_at'] * 1000,
                'cancel_time' => 0,
                'state' => (int) $txn['status'],
                'transaction' => (string) $txn['id'],
                'reason' => null
            ],
            'id' => $id
        ];
    }
}
