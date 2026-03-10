<?php

namespace backend\controllers;

use Yii;
use common\models\User;
use backend\models\UserForm;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * UserController provisions logical RBAC and staff mapping centrally
 */
class UserController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        // Super Admin strictly delegates staff mapping internally by default natively
                        'roles' => ['superAdmin'],
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
        $dataProvider = new ActiveDataProvider([
            'query' => User::find()->with(['employee', 'branch']),
            'pagination' => ['pageSize' => 20],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate()
    {
        $model = new UserForm();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Yangi xodim saqlandi');
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate($id)
    {
        $user = $this->findModel($id);

        if ($user->id === 1 && Yii::$app->user->id !== 1) {
            throw new \yii\web\ForbiddenHttpException("Asosiy admin huquqini o'zgartirib bo'lmaydi.");
        }

        $model = new UserForm();
        $model->setModel($user);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Xodim ma\'lumotlari yangilandi');
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionDelete($id)
    {
        $user = $this->findModel($id);
        if ($user->id === 1) {
            Yii::$app->session->setFlash('error', "Root adminni o'chirish taqiqlanadi.");
        } elseif ($user->id === Yii::$app->user->id) {
            Yii::$app->session->setFlash('error', "O'zingizni o'chira olmaysiz.");
        } else {
            $user->status = User::STATUS_DELETED; // Soft delete natively
            $user->save(false);
            Yii::$app->session->setFlash('info', 'Foydalanuvchi tizimdan olib tashlandi.');
        }

        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Sahifa topilmadi.');
    }
}
