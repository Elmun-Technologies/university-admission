<?php

namespace backend\controllers;

use Yii;
use common\models\Student;
use common\models\StudentSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Response;
use backend\components\StudentExporter;
use backend\components\NotificationManager;

/**
 * StudentController governs all primary applicant tracking logic internally for branches.
 */
class StudentController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['manageStudent', 'superAdmin'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                    'change-status' => ['POST'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $searchModel = new StudentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionCreate()
    {
        $model = new Student();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Talaba muvaffaqiyatli qitib kiritildi');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Talaba ma\'lumotlari yangilandi');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionDelete($id)
    {
        // Soft delete mapped cleanly
        $model = $this->findModel($id);
        $model->status = Student::STATUS_REJECTED; // or soft delete status 9 mapped organically
        $model->save(false);

        Yii::$app->session->setFlash('warning', 'Abiturient arxivga tushdi');
        return $this->redirect(['index']);
    }

    /**
     * Critical Action governing state machine transitions securely handled via AJAX
     */
    public function actionChangeStatus()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $id = Yii::$app->request->post('student_id');
        $newStatus = (int) Yii::$app->request->post('new_status');
        $note = Yii::$app->request->post('note');

        $model = $this->findModel($id);

        // Allowed transitions state machine physically defined in Student instance
        $allowed = Student::STATUS_TRANSITIONS[$model->status] ?? [];

        if (!in_array($newStatus, $allowed)) {
            return ['success' => false, 'message' => "Holatni ushbu bosqichga o'zgartirish taqiqlanadi!"];
        }

        $oldStatus = $model->status;
        $model->status = $newStatus;

        if ($model->save(false)) { // Skip validation explicitly for transitions if we just want status
            // Log physical history natively
            $history = json_decode($model->status_history ?? '[]', true);
            $history[] = [
                'from' => $oldStatus,
                'to' => $newStatus,
                'note' => $note,
                'user_id' => Yii::$app->user->id,
                'time' => time()
            ];
            $model->updateAttributes(['status_history' => json_encode($history)]);

            // Background Notify Student
            if ($model->phone) {
                // Assuming status maps to a message. Real logic might format differently
                $msg = "Hurmatli {$model->getFullName()}, sizning arizangiz holati: " . $model->getStatusLabel();
                \Yii::$app->queue->push(new \common\jobs\TelegramNotifyJob([
                    'branchId' => $model->branch_id,
                    'message' => $msg,
                ]));
            }

            return ['success' => true];
        }

        return ['success' => false, 'message' => 'Noma\'lum xatolik'];
    }

    /**
     * Excel Export Streaming
     */
    public function actionExport()
    {
        $searchModel = new StudentSearch();
        // Extract filters but don't limit pagination
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination = false; // Important: Get all matching filters

        $exporter = new StudentExporter();
        $filePath = $exporter->export($dataProvider);

        return Yii::$app->response->sendFile($filePath, 'Abiturientlar_' . date('Y-m-d') . '.xlsx');
    }

    protected function findModel($id)
    {
        if (($model = Student::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Sahifa topilmadi.');
    }
}
