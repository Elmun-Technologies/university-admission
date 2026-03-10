<?php

namespace frontend\controllers;

use common\models\Student;
use common\models\StudentOferta;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * ContractController uniquely handles agreement viewing and digital signing
 */
class ContractController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    protected function findStudentModel()
    {
        $student = Student::findOne(['created_by' => Yii::$app->user->id]);
        if (!$student) {
            throw new NotFoundHttpException('Profile not found.');
        }
        return $student;
    }

    /**
     * Show contract payload mapped to UI
     */
    public function actionView()
    {
        $student = $this->findStudentModel();

        // Must have passed exam to see contract logically
        if ($student->status < Student::STATUS_EXAM_PASSED) {
            Yii::$app->session->setFlash('warning', Yii::t('app', 'Shartnomani ko\'rish huquqiga ega emassiz.'));
            return $this->redirect(['/dashboard/index']);
        }

        // Auto-generate Oferta if it doesn't physically exist yet
        $oferta = StudentOferta::findOne(['student_id' => $student->id]);

        if (!$oferta) {
            $oferta = new StudentOferta();
            $oferta->student_id = $student->id;
            $oferta->contract_number = StudentOferta::generateContractNumber($student->branch_id);
            // Default pricing bound heavily to Direction rules
            $oferta->payment_amount = $student->direction->tuition_fee;
            $oferta->payment_status = StudentOferta::PAYMENT_UNPAID;
            $oferta->save(false);
        }

        return $this->render('view', [
            'student' => $student,
            'oferta' => $oferta,
        ]);
    }

    /**
     * Action to digitally stamp and "Sign"
     */
    public function actionSign()
    {
        // Simple POST endpoint driven by Checkbox Agree button
        if (Yii::$app->request->isPost) {
            $student = $this->findStudentModel();
            $oferta = StudentOferta::findOne(['student_id' => $student->id]);

            if ($oferta && !$oferta->signed_at) {
                // Ensure they actually checked the box via POST
                if (Yii::$app->request->post('agree_terms')) {
                    $transaction = Yii::$app->db->beginTransaction();
                    try {
                        $oferta->signed_at = time();
                        $oferta->save(false);

                        // Advance Student state
                        $student->logStatusChange(Student::STATUS_CONTRACT_SIGNED, Yii::$app->user->id, "Shartnoma elektron tarzda imzolandi");
                        $student->save(false);

                        $transaction->commit();
                        Yii::$app->session->setFlash('success', Yii::t('app', 'Shartnoma muvaffaqiyatli imzolandi! Endi to\'lov qismini amalga oshirishingiz mumkin.'));

                        return $this->redirect(['/payment/index']);
                    } catch (\Exception $e) {
                        $transaction->rollBack();
                        Yii::$app->session->setFlash('error', 'Tizim xatosi.');
                    }
                } else {
                    Yii::$app->session->setFlash('error', Yii::t('app', 'Shartlarni qabul qilishingiz shart.'));
                }
            }
        }
        return $this->redirect(['view']);
    }

    /**
     * Dedicated HTML Render intended for physical CTRL+P or future wkhtmltopdf hooks
     */
    public function actionDownload()
    {
        $this->layout = false; // Disable global wrapper entirely for pure A4 sizing

        $student = $this->findStudentModel();
        $oferta = StudentOferta::findOne(['student_id' => $student->id]);

        if (!$oferta || !$oferta->signed_at) {
            throw new NotFoundHttpException('Signed contract not found.');
        }

        return $this->render('template', [
            'student' => $student,
            'oferta' => $oferta,
            'data' => $oferta->generateContractPdf()
        ]);
    }
}
