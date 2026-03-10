<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\UploadedFile;
use backend\components\InitialDataImporter;
use yii\filters\AccessControl;

/**
 * ImportController handles file uploads for initial data loading
 */
class ImportController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['superAdmin'],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionDirections()
    {
        $file = UploadedFile::getInstanceByName('file');
        if ($file) {
            $path = Yii::getAlias('@runtime/import_' . time() . '.' . $file->extension);
            $file->saveAs($path);

            $importer = new InitialDataImporter();
            if ($importer->importDirections($path)) {
                Yii::$app->session->setFlash('success', "{$importer->getSuccessCount()} ta yo'nalish yuklandi.");
            } else {
                Yii::$app->session->setFlash('error', "Xatoliklar: " . implode('<br>', $importer->getErrors()));
            }
            @unlink($path);
        }
        return $this->redirect(['index']);
    }

    public function actionQuestions($subject_id)
    {
        $file = UploadedFile::getInstanceByName('file');
        if ($file) {
            $path = Yii::getAlias('@runtime/q_import_' . time() . '.' . $file->extension);
            $file->saveAs($path);

            $importer = new InitialDataImporter();
            $importer->importQuestions($path, $subject_id);

            Yii::$app->session->setFlash('info', "Savollar yuklash yakunlandi. Muvaffaqiyatli: " . $importer->getSuccessCount());
            @unlink($path);
        }
        return $this->redirect(['/exam/questions', 'subject_id' => $subject_id]);
    }
}
