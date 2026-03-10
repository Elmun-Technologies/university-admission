<?php

namespace frontend\controllers;

use common\models\Student;
use common\models\Direction;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

/**
 * ProfileController handles the applicant data gathering pipeline
 */
class ProfileController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'], // Must be logged in
                    ],
                ],
            ],
        ];
    }

    /**
     * Helper to get current active student profile
     */
    protected function findStudentModel()
    {
        $student = Student::findOne(['created_by' => Yii::$app->user->id]);
        if (!$student) {
            throw new NotFoundHttpException('Profile not found.');
        }
        return $student;
    }

    /**
     * Determine next step logic dynamically
     */
    public function actionIndex()
    {
        $student = $this->findStudentModel();

        if (empty($student->birth_date) || empty($student->gender)) {
            return $this->redirect(['personal']);
        }
        if (empty($student->passport_series) || empty($student->pinfl)) {
            return $this->redirect(['documents']);
        }
        if (empty($student->photo)) {
            return $this->redirect(['photo']);
        }
        if (empty($student->direction_id)) {
            return $this->redirect(['direction']);
        }

        // Entire anketa is filled
        if ($student->status == Student::STATUS_NEW) {
            $student->logStatusChange(Student::STATUS_ANKETA, Yii::$app->user->id, "Anketa to'liq to'ldirildi");
            $student->save(false);
        }

        return $this->redirect(['/dashboard/index']);
    }

    /**
     * Step 1: Personal Info
     */
    public function actionPersonal()
    {
        $model = $this->findStudentModel();

        // Setup initial scenario if needed, but we'll manually validate specific fields
        if ($model->load(Yii::$app->request->post())) {
            // we only want to validate/save these specific fields in this step
            if ($model->save(true, ['first_name', 'last_name', 'middle_name', 'first_name_ru', 'last_name_ru', 'middle_name_ru', 'birth_date', 'gender', 'phone2', 'email'])) {
                return $this->redirect(['documents']);
            }
        }

        return $this->render('personal', [
            'model' => $model,
        ]);
    }

    /**
     * Step 2: Passport & Address
     */
    public function actionDocuments()
    {
        $model = $this->findStudentModel();

        if ($model->load(Yii::$app->request->post())) {

            // Format strictly for DB saves
            $model->passport_series = strtoupper(trim($model->passport_series));

            if ($model->save(true, ['passport_series', 'passport_number', 'passport_given_by', 'passport_given_date', 'pinfl', 'region_id', 'district_id', 'address'])) {
                return $this->redirect(['photo']);
            }
        }

        return $this->render('documents', [
            'model' => $model,
        ]);
    }

    /**
     * Step 3: Photo Upload and GD Resize
     */
    public function actionPhoto()
    {
        $model = $this->findStudentModel();

        if (Yii::$app->request->isPost) {
            $photoFile = UploadedFile::getInstance($model, 'photo');

            if ($photoFile) {
                $dir = Yii::getAlias('@frontend/web/uploads/photos/');
                if (!is_dir($dir)) {
                    mkdir($dir, 0777, true);
                }

                $fileName = $model->id . '_' . time() . '.jpg';
                $filePath = $dir . $fileName;

                if ($photoFile->saveAs($filePath)) {
                    // Start GD Resize to max 400x500
                    $this->resizeImage($filePath, 400, 500);

                    // Save reference to DB
                    $model->photo = 'uploads/photos/' . $fileName;
                    if ($model->save(false, ['photo'])) {
                        return $this->redirect(['direction']);
                    }
                }
            } else {
                $model->addError('photo', Yii::t('app', 'Iltimos rasmni yuklang. / Пожалуйста загрузите фото.'));
            }
        }

        return $this->render('photo', [
            'model' => $model,
        ]);
    }

    /**
     * Step 4: Direction selection visually
     */
    public function actionDirection()
    {
        $model = $this->findStudentModel();

        // Fetch active directions for the specific branch of this student
        $directions = Direction::getActiveByBranch($model->branch_id);

        if ($model->load(Yii::$app->request->post())) {
            if ($model->save(true, ['direction_id', 'edu_form_id', 'edu_type_id'])) {
                // If it's a transfer type internally handled, we could branch here.
                return $this->redirect(['index']); // triggers the final completeness check
            }
        }

        return $this->render('direction', [
            'model' => $model,
            'directions' => $directions,
        ]);
    }

    /**
     * Internal logic helper to aggressively compress and resize images via pure GD
     */
    private function resizeImage($file, $w, $h)
    {
        list($width, $height, $type) = getimagesize($file);

        // Calculate new proportions safely
        $ratio = min($w / $width, $h / $height);
        $new_width = $width * $ratio;
        $new_height = $height * $ratio;

        // Create canvas
        $src = null;
        if ($type == IMAGETYPE_JPEG)
            $src = imagecreatefromjpeg($file);
        elseif ($type == IMAGETYPE_PNG) {
            $src = imagecreatefrompng($file);
            imagepalettetotruecolor($src);
        }

        if ($src) {
            $dst = imagecreatetruecolor($new_width, $new_height);
            imagecopyresampled($dst, $src, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
            imagejpeg($dst, $file, 85); // Compress 85 quality
            imagedestroy($src);
            imagedestroy($dst);
        }
    }
}
