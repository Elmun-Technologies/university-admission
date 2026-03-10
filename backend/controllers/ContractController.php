<?php

namespace backend\controllers;

use Yii;
use common\models\StudentOferta;
use common\models\Student;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use common\components\ContractPdfGenerator;

/**
 * ContractController implements internal actions for StudentOferta model.
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
                        // Bound specifically to users possessing viewContract role logic
                        'roles' => ['viewContract', 'superAdmin'],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $query = StudentOferta::find()->joinWith(['student.direction']);

        // Scope strictly by branch if not superAdmin organically
        if (Yii::$app->user->identity->branch_id) {
            $query->andWhere(['student.branch_id' => Yii::$app->user->identity->branch_id]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 20],
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]]
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Physical Partial Payment execution
     */
    public function actionUpdatePayment($id)
    {
        $model = $this->findModel($id);

        // Basic permissions mapping natively
        if (!Yii::$app->user->can('managePayment') && Yii::$app->user->id !== 1) {
            throw new \yii\web\ForbiddenHttpException('Sizda to\'lov kiritish huquqi yo\'q!');
        }

        if (Yii::$app->request->isPost) {
            $amount = (float) Yii::$app->request->post('amount', 0);
            $date = Yii::$app->request->post('date', date('Y-m-d'));
            $method = Yii::$app->request->post('method', '');

            if ($amount > 0) {
                // We add to existing mapping
                $model->payment_amount += $amount;
                $model->payment_date = $date;
                $model->payment_method = $method;

                // Threshold cross
                if ($model->payment_amount >= $model->contract_amount) {
                    $model->payment_amount = $model->contract_amount; // Cap physically
                    $model->payment_status = StudentOferta::PAYMENT_PAID;

                    // Trigger System Sync Status
                    $student = $model->student;
                    if ($student->status == Student::STATUS_CONTRACT_SIGNED) {
                        $student->status = Student::STATUS_PAID;
                        $student->save(false);
                    }
                } else {
                    $model->payment_status = StudentOferta::PAYMENT_PARTIAL;
                }

                if ($model->save(false)) {
                    Yii::$app->session->setFlash('success', "To'lov muvaffaqiyatli qabul qilindi: " . number_format($amount, 0, '', ' ') . ' UZS');
                } else {
                    Yii::$app->session->setFlash('error', "To'lovni saqlashda xatolik yuz berdi");
                }
            }
        }

        // Bounce back strictly to the Student Profile UI Context 
        return $this->redirect(['/student/view', 'id' => $model->student_id, '#' => 'contract']);
    }

    /**
     * Proxy Stream endpoint mapping to underlying mPDF component securely
     */
    public function actionDownloadPdf($id)
    {
        $model = $this->findModel($id);

        $generator = new ContractPdfGenerator();
        $filePath = $generator->generate($model);

        if ($filePath && file_exists($filePath)) {
            return Yii::$app->response->sendFile($filePath, "Kontrakt_{$model->contract_number}.pdf");
        }

        Yii::$app->session->setFlash('error', 'PDF faylni yaratishda xatolik yuz berdi');
        return $this->redirect(Yii::$app->request->referrer);
    }

    protected function findModel($id)
    {
        if (($model = StudentOferta::findOne($id)) !== null) {
            // Apply bounds natively
            if (Yii::$app->user->identity->branch_id && $model->student->branch_id != Yii::$app->user->identity->branch_id && Yii::$app->user->id !== 1) {
                throw new \yii\web\ForbiddenHttpException();
            }
            return $model;
        }

        throw new NotFoundHttpException('Sahifa topilmadi.');
    }
}
