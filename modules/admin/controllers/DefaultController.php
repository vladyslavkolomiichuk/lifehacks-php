<?php

namespace app\modules\admin\controllers;

use Yii;
use yii\web\Controller;
use app\models\Article;
use app\models\User;
use app\models\Comment;
use app\models\Topic;
use yii\filters\AccessControl;

/**
 * Admin default controller (dashboard).
 */
class DefaultController extends Controller
{
  /**
   * Access control for admin users.
   */
  public function behaviors()
  {
    return [
      'access' => [
        'class' => AccessControl::class,
        'rules' => [
          [
            'allow' => true,
            'roles' => ['@'], // Only authenticated users
            'matchCallback' => function ($rule, $action) {
              return Yii::$app->user->identity->isAdmin; // Only admins
            },
          ],
        ],
      ],
    ];
  }

  /**
   * Displays the admin dashboard with statistics.
   */
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
