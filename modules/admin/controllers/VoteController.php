<?php

namespace app\modules\admin\controllers;

use Yii;
use app\models\Vote;
use app\models\VoteSearch;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * Admin controller for managing votes.
 */
class VoteController extends Controller
{
  /**
   * Access control and HTTP verb filters.
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
      'verbs' => [
        'class' => VerbFilter::class,
        'actions' => ['delete' => ['POST']],
      ],
    ];
  }

  /**
   * Lists all votes with search and filter.
   */
  public function actionIndex()
  {
    $searchModel = new VoteSearch();
    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

    return $this->render('index', [
      'searchModel' => $searchModel,
      'dataProvider' => $dataProvider,
    ]);
  }

  /**
   * Deletes a vote and decrements the article upvotes.
   */
  public function actionDelete($id)
  {
    $vote = Vote::findOne($id);

    if ($vote) {
      $vote->article->updateCounters(['upvotes' => -1]);
      $vote->delete();
    }

    return $this->redirect(['index']);
  }

  /**
   * Displays a single vote.
   */
  public function actionView($id)
  {
    return $this->render('view', [
      'model' => Vote::findOne($id),
    ]);
  }
}
