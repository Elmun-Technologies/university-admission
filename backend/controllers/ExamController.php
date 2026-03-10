<?php

namespace backend\controllers;

use Yii;
use common\models\Exam;
use common\models\ExamDate;
use common\models\Question;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\data\ActiveDataProvider;
use backend\components\QuestionImporter;
use yii\web\UploadedFile;
use yii\web\Response;

class ExamController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['manageExam', 'superAdmin'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                    'import-questions' => ['POST'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Exam::find()->orderBy(['id' => SORT_DESC]),
            'pagination' => ['pageSize' => 20],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionSchedule($examId)
    {
        $exam = Exam::findOne($examId);
        if (!$exam)
            throw new \yii\web\NotFoundHttpException("Imtihon topilmadi");

        // Handle quick schedule creation
        if (Yii::$app->request->isPost) {
            $date = new ExamDate();
            $date->exam_id = $exam->id;
            $date->exam_date = Yii::$app->request->post('exam_date');
            $date->start_time = Yii::$app->request->post('start_time');
            $date->max_participants = Yii::$app->request->post('max_participants', 50);

            if ($date->save()) {
                Yii::$app->session->setFlash('success', 'Yangi sana qo\'shildi');
            }
            return $this->refresh();
        }

        $dates = ExamDate::find()->where(['exam_id' => $examId])->orderBy(['exam_date' => SORT_ASC, 'start_time' => SORT_ASC])->all();

        return $this->render('schedule', [
            'exam' => $exam,
            'dates' => $dates,
        ]);
    }

    public function actionQuestions($examId)
    {
        $exam = Exam::findOne($examId);

        // Find subject associations linked to this exam's direction natively
        $subjects = $exam->direction->directionSubjects ?? [];

        return $this->render('questions', [
            'exam' => $exam,
            'subjects' => $subjects,
        ]);
    }

    /**
     * Handles physical Excel File Upload mapping directly to QuestionImporter Service
     */
    public function actionImportQuestions()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $subjectId = Yii::$app->request->post('subject_id');
        $examId = Yii::$app->request->post('exam_id');

        $file = UploadedFile::getInstanceByName('excel_file');

        if (!$file) {
            return ['success' => false, 'message' => 'Fayl yuklanmadi'];
        }

        $path = Yii::getAlias('@runtime/') . $file->name;
        $file->saveAs($path);

        $importer = new QuestionImporter();
        $parsedData = $importer->parseExcel($path, $subjectId);

        // Unlink temp storage physically
        @unlink($path);

        if (empty($parsedData)) {
            return ['success' => false, 'message' => 'Fayl ichidan savollar topilmadi. Shablon (Template) ga mos kelishini tekshiring.'];
        }

        $validation = $importer->validate($parsedData);
        if (!empty($validation)) {
            return [
                'success' => false,
                'message' => 'Qator xatolari topildi. Iltimos tekshiring.',
                'errors' => $validation
            ];
        }

        $result = $importer->import($parsedData, $examId);

        return [
            'success' => true,
            'message' => "Muvaffaqiyatli {$result['success_count']} ta savol yuklandi."
        ];
    }
}
