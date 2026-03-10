<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use common\models\Help;

/**
 * HelpController implements the FAQ and Help section for students.
 */
class HelpController extends Controller
{
    /**
     * Displays the main Help page grouped by categories.
     * @return string
     */
    public function actionIndex()
    {
        $faqs = Help::find()
            ->where(['is_active' => true])
            ->orderBy(['sort_order' => SORT_ASC])
            ->all();

        // Group by category depending on current language
        $groupedFaqs = [];
        foreach ($faqs as $faq) {
            $cat = $faq->getCategory();
            if (!isset($groupedFaqs[$cat])) {
                $groupedFaqs[$cat] = [];
            }
            $groupedFaqs[$cat][] = $faq;
        }

        return $this->render('index', [
            'groupedFaqs' => $groupedFaqs,
        ]);
    }

    /**
     * AJAX Search for Help articles
     * @return json
     */
    public function actionSearch($q)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $lang = Yii::$app->language;
        $qField = $lang === 'ru' ? 'question_ru' : 'question_uz';
        $aField = $lang === 'ru' ? 'answer_ru' : 'answer_uz';

        $results = Help::find()
            ->where(['is_active' => true])
            ->andWhere(['or',
                ['like', $qField, $q],
                ['like', $aField, $q]
            ])
            ->limit(10)
            ->all();

        $data = [];
        foreach ($results as $item) {
            $data[] = [
                'id' => $item->id,
                'question' => $item->getQuestion(),
                'answer' => $item->getAnswer(),
            ];
        }

        return $data;
    }
}
