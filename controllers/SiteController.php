<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;

class SiteController extends Controller
{
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    // Якщо хочете, щоб головна сторінка сайту "/" відкривала стрічку новин,
    // можна зробити редірект або відрендерити actionIndex з ArticleController
    public function actionIndex()
    {
        return $this->redirect(['article/index']);
    }
}
