<?php

namespace backend\controllers;

use Yii;
use common\models\Direction;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Response;

/**
 * DirectionController implements the CRUD actions for Direction model mapping.
 */
class DirectionController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        // Bound specifically to users possessing branch configuration access
                        'roles' => ['manageDirection', 'superAdmin'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                    'toggle-status' => ['POST'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        // Simple provider assuming single branch scoped by default natively
        $query = Direction::find();
        if (Yii::$app->user->identity->branch_id) {
            $query->where(['branch_id' => Yii::$app->user->identity->branch_id]);
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

    /**
     * AJAX Toggle switch mapping
     */
    public function actionToggleStatus($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = $this->findModel($id);

        // Ensure user has branch ownership (Simple guard)
        if (Yii::$app->user->identity->branch_id && $model->branch_id != Yii::$app->user->identity->branch_id && Yii::$app->user->id !== 1) {
            return ['success' => false, 'message' => 'Ruxsat etilmagan'];
        }

        $model->status = $model->status == Direction::STATUS_ACTIVE ? Direction::STATUS_INACTIVE : Direction::STATUS_ACTIVE;

        if ($model->save(false)) {
            return ['success' => true, 'new_status' => $model->status];
        }
        return ['success' => false];
    }

    public function actionCreate()
    {
        $model = new Direction();
        $model->branch_id = Yii::$app->user->identity->branch_id; // Default explicitly
        $model->status = Direction::STATUS_ACTIVE;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Yo\'nalish saqlandi');
            return $this->redirect(['index']); // Usually redirection back to index
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            // Handle relations natively (Forms, Types, Subjects arrays submitted via Tabbed form)
            $post = Yii::$app->request->post();

            // Logic mapping pivot tables would conventionally execute here
            // array_map() insert mechanisms for EduForms and Subject orderings.
            // Simplified for layout scaffold:

            Yii::$app->session->setFlash('success', 'Yo\'nalish yangilandi');
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        Yii::$app->session->setFlash('info', 'Yo\'nalish o\'chirildi');
        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (
            ($model = Direction::findOne([
                'id' => $id,
                // Scope restriction organically
                'branch_id' => Yii::$app->user->identity->branch_id ?: null
            ])) !== null
        ) {
            return $model;
        }

        throw new NotFoundHttpException('Bunday sahifa mavjud emas.');
    }
}
