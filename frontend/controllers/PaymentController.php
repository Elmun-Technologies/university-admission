<?php

namespace frontend\controllers;

use common\models\Student;
use common\models\StudentOferta;
use common\components\FakePaymentGateway;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * PaymentController handles the mock financial endpoints
 */
class PaymentController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                // Only applicant can initiate, but the webhook logic could technically be public
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'initiate', 'success', 'failed'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['callback'],
                        'roles' => ['?', '@'], // Webhooks from Click/Payme arrive unauthenticated
                    ]
                ],
            ],
        ];
    }

    protected function findStudentModel()
    {
        $student = Student::findOne(['created_by' => Yii::$app->user->id]);
        if (!$student)
            throw new NotFoundHttpException('Profile not found.');
        return $student;
    }

    /**
     * Show payment method selectors
     */
    public function actionIndex()
    {
        $student = $this->findStudentModel();

        if ($student->status < Student::STATUS_CONTRACT_SIGNED) {
            return $this->redirect(['/dashboard/index']);
        }

        $oferta = StudentOferta::findOne(['student_id' => $student->id]);

        return $this->render('index', [
            'student' => $student,
            'oferta' => $oferta
        ]);
    }

    /**
     * Redirect to the external gateway
     */
    public function actionInitiate()
    {
        $student = $this->findStudentModel();
        $oferta = StudentOferta::findOne(['student_id' => $student->id]);

        if ($oferta && $oferta->payment_status != StudentOferta::PAYMENT_PAID) {

            // For production, inject Click or Payme component. Using Mock interface natively here.
            $gateway = new FakePaymentGateway();

            $returnUrl = \Yii::$app->urlManager->createAbsoluteUrl(['/payment/success']);
            $redirectUrl = $gateway->initPayment($oferta->payment_amount, $oferta->id, $returnUrl);

            return $this->redirect($redirectUrl);
        }

        return $this->redirect(['index']);
    }

    /**
     * Listen for Webhook triggers from Mock Interface
     */
    public function actionCallback($token, $redir)
    {
        // Normally this reads raw POST JSON data from Payme/Click via file_get_contents('php://input')
        // We are using a GET simulation for the FakePayment Gateway
        $gateway = new FakePaymentGateway();

        if ($gateway->verifyPayment($token)) {

            // Must lookup which student this token belonged to manually since we bypass Identity context here
            $decoded = base64_decode($token);
            $parts = explode('_', $decoded);
            $oferta = StudentOferta::findOne($parts[1]);

            if ($oferta) {
                // Elevate Student Status!
                $student = $oferta->student;
                $student->logStatusChange(Student::STATUS_PAID, $student->created_by, "To'lov muvaffaqiyatli qabul qilindi (Tizimli tasdiq)");
                $student->save(false);

                // If Consulting Agency brought them, calculate commission instantly
                if ($student->consulting_id) {
                    $consultingMap = \common\models\ConsultingStudent::findOne(['student_id' => $student->id]);
                    if ($consultingMap) {
                        $consultingMap->calculateCommission();
                    }
                }
            }

            return $this->redirect($redir); // Send user to success bound route
        }

        return $this->redirect(['failed']);
    }

    public function actionSuccess()
    {
        return $this->render('success');
    }

    public function actionFailed()
    {
        return $this->render('failed');
    }
}
