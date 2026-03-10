<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use common\models\Student;
use yii\filters\AccessControl;

class PrintController extends Controller
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

    protected function findStudentModel($id)
    {
        // Must belong to the logged in user
        $model = Student::findOne(['id' => $id, 'created_by' => Yii::$app->user->id]);
        if ($model !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Sahifa topilmadi. / Страница не найдена.');
    }

    /**
     * Ruxsatnoma (Admit Card) PDF
     */
    public function actionRuxsatnoma($id)
    {
        $student = $this->findStudentModel($id);

        if (!$student->canTakeExam()) {
            Yii::$app->session->setFlash('warning', 'Imtihonga ruxsatnoma hali tayyor emas.');
            return $this->redirect(['/dashboard/index']);
        }

        // Generate PDF using mPDF
        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'default_font' => 'dejavusans'
        ]);

        $mpdf->SetTitle('Imtihon Ruxsatnomasi - ' . $student->getFullName());

        $content = $this->renderPartial('ruxsatnoma', [
            'student' => $student,
        ]);

        $mpdf->WriteHTML($content);
        return $mpdf->Output('Ruxsatnoma_' . $student->pinfl . '.pdf', 'I');
    }

    /**
     * Shartnoma (Contract) PDF
     */
    public function actionShartnoma($id)
    {
        $student = $this->findStudentModel($id);

        // Only passed students or Contract Signed can download contract
        if (!in_array($student->status, [Student::STATUS_EXAM_PASSED, Student::STATUS_CONTRACT_SIGNED, Student::STATUS_PAID])) {
            Yii::$app->session->setFlash('warning', 'Shartnoma hali shakllanmagan.');
            return $this->redirect(['/dashboard/index']);
        }

        // Generate PDF using mPDF
        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'default_font' => 'dejavusans'
        ]);

        $mpdf->SetTitle('Shartnoma - ' . $student->getFullName());

        $content = $this->renderPartial('shartnoma', [
            'student' => $student,
        ]);

        $mpdf->WriteHTML($content);
        return $mpdf->Output('Shartnoma_' . $student->pinfl . '.pdf', 'I');
    }
}
