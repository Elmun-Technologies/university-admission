<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use common\models\Branch;

/**
 * SettingsController orchestrates Branch-specific dynamic configurations internally
 */
class SettingsController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        // Bound to SuperAdmin strictly mostly, or branch head natively
                        'roles' => ['manageSettings', 'superAdmin'],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        // For simplicity, mapping current User's Branch logically
        $branchId = Yii::$app->user->identity->branch_id;
        $model = Branch::findOne($branchId) ?? new Branch();

        if ($model->load(Yii::$app->request->post())) {
            // Assume ImageUpload behavior natively attached
            $model->logo = \yii\web\UploadedFile::getInstance($model, 'logo');

            if ($model->save()) {
                if ($model->logo) {
                    $path = Yii::getAlias('@frontend/web/uploads/branches/') . $model->logo->baseName . '.' . $model->logo->extension;
                    $model->logo->saveAs($path);
                    $model->updateAttributes(['logo' => $model->logo->baseName . '.' . $model->logo->extension]);
                }

                Yii::$app->session->setFlash('success', 'Sozlamalar muvaffaqiyatli saqlandi');
                return $this->refresh();
            }
        }

        // Generate JSON mapped configs natively physically stored in config_data column
        $configs = json_decode($model->config_data ?? '{}', true);

        return $this->render('index', [
            'model' => $model,
            'configs' => $configs
        ]);
    }

    /**
     * Dedicated Telegram Bot Setup Endpoint mapped organically
     */
    public function actionTelegram()
    {
        if (Yii::$app->request->isPost) {
            $branchId = Yii::$app->user->identity->branch_id;
            $model = Branch::findOne($branchId);

            if ($model) {
                $configs = json_decode($model->config_data ?? '{}', true);
                $configs['telegram_bot_token'] = Yii::$app->request->post('bot_token');
                $configs['telegram_chat_id'] = Yii::$app->request->post('chat_id');

                $model->updateAttributes(['config_data' => json_encode($configs)]);
                Yii::$app->session->setFlash('success', 'Telegram sozlamalari saqlandi. Zamin xabarnomalari ishga tushirildi.');
            }
        }
        return $this->redirect(['index', '#' => 'telegram']);
    }

    /**
     * AmoCRM Gateway Configuration mapped organically
     */
    public function actionCrm()
    {
        if (Yii::$app->request->isPost) {
            $branchId = Yii::$app->user->identity->branch_id;
            $model = Branch::findOne($branchId);

            if ($model) {
                $configs = json_decode($model->config_data ?? '{}', true);
                $configs['amocrm_domain'] = Yii::$app->request->post('amocrm_domain');
                $configs['amocrm_client_id'] = Yii::$app->request->post('amocrm_client_id');
                $configs['amocrm_secret'] = Yii::$app->request->post('amocrm_secret');

                $model->updateAttributes(['config_data' => json_encode($configs)]);
                Yii::$app->session->setFlash('success', 'AmoCRM integratsiyasi sozlandi. Queue tizimi uzatishni boshlaydi.');
            }
        }
        return $this->redirect(['index', '#' => 'crm']);
    }
}
