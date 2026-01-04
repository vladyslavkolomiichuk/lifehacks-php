<?php

namespace app\modules\admin\controllers;

use yii\web\Controller;
use app\models\Article;
use app\models\User;
use app\models\Comment;
use app\models\Topic;

class DefaultController extends Controller
{
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
