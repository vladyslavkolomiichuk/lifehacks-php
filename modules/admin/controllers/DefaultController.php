<?php

namespace app\modules\admin\controllers;

use Yii;
use yii\web\Controller;
use app\models\Article;
use app\models\User;
use app\models\Comment;
use app\models\Topic;
use yii\filters\AccessControl;

class DefaultController extends Controller
{
  public function behaviors()
  {
    return [
      'access' => [
        'class' => AccessControl::class,
        'rules' => [
          [
            'allow' => true,
            'roles' => ['@'],
            'matchCallback' => function ($rule, $action) {
              return Yii::$app->user->identity->isAdmin;
            }
          ],
        ],
      ],
    ];
  }

  public function actionIndex()
  {
    return $this->render('index', [
      'countUsers' => User::find()->count(),
      'countArticles' => Article::find()->count(),
      'countComments' => Comment::find()->count(),
      'countTopics' => Topic::find()->count(),
      'totalViews' => Article::find()->sum('viewed'),
    ]);
  }
}
