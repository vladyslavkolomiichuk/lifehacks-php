<?php

namespace app\modules\admin\controllers;

use Yii;
use app\models\Comment;
use app\models\CommentSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * Admin controller for managing comments.
 */
class CommentController extends Controller
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
   * Lists all comments with search and filter.
   */
  public function actionIndex()
  {
    $searchModel = new CommentSearch();
    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

    return $this->render('index', [
      'searchModel' => $searchModel,
      'dataProvider' => $dataProvider,
    ]);
  }

  /**
   * Displays a single comment.
   */
  public function actionView($id)
  {
    return $this->render('view', [
      'model' => $this->findModel($id),
    ]);
  }

  /**
   * Updates a comment (moderation).
   */
  public function actionUpdate($id)
  {
    $model = $this->findModel($id);

    if ($model->load(Yii::$app->request->post()) && $model->save()) {
      return $this->redirect(['index']);
    }

    return $this->render('update', ['model' => $model]);
  }

  /**
   * Deletes a comment.
   */
  public function actionDelete($id)
  {
    $this->findModel($id)->delete();
    return $this->redirect(['index']);
  }

  /**
   * Finds the Comment model by ID or throws 404.
   */
  protected function findModel($id)
  {
    if (($model = Comment::findOne($id)) !== null) {
      return $model;
    }

    throw new NotFoundHttpException('Page not found.');
  }
}
