<?php

namespace backend\controllers;

use Yii;
use common\models\Consulting;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * ConsultingController implements the CRUD actions for Consulting models strictly securely
 */
class ConsultingController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        // Bound physically internally
                        'roles' => ['manageConsulting', 'superAdmin'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $query = Consulting::find();

        if (Yii::$app->user->identity->branch_id) {
            // Usually global but if strictly scoped securely:
            // $query->where(['branch_id' => Yii::$app->user->identity->branch_id]);
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

    public function actionCreate()
    {
        $model = new Consulting();
        $model->status = 1;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Konsalting tashkiloti qo\'shildi');
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Ma\'lumotlar saqlandi');
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        // Prevent deletion if linked natively
        if ($model->getStudents()->count() > 0) {
            Yii::$app->session->setFlash('error', 'O\'chirib bo\'lmaydi: Bu tashkilotga bog\'langan abiturientlar mavjud!');
        } else {
            $model->delete();
            Yii::$app->session->setFlash('info', 'Tashkilot o\'chirildi');
        }

        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = Consulting::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Sahifa topilmadi.');
    }
}
