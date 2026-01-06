<?php

namespace app\modules\admin\controllers;

use Yii;
use app\models\Topic;
use app\models\TopicSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * Admin controller for managing topics.
 */
class TopicController extends Controller
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
   * Lists all topics with search and filter.
   */
  public function actionIndex()
  {
    $searchModel = new TopicSearch();
    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

    return $this->render('index', [
      'searchModel' => $searchModel,
      'dataProvider' => $dataProvider,
    ]);
  }

  /**
   * Creates a new topic.
   */
  public function actionCreate()
  {
    $model = new Topic();

    if ($model->load(Yii::$app->request->post()) && $model->save()) {
      return $this->redirect(['index']);
    }

    return $this->render('create', ['model' => $model]);
  }

  /**
   * Updates an existing topic.
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
   * Deletes a topic.
   */
  public function actionDelete($id)
  {
    $this->findModel($id)->delete();
    return $this->redirect(['index']);
  }

  /**
   * Displays a single topic.
   */
  public function actionView($id)
  {
    return $this->render('view', [
      'model' => $this->findModel($id),
    ]);
  }

  /**
   * Finds the Topic model by ID or throws 404.
   */
  protected function findModel($id)
  {
    if (($model = Topic::findOne($id)) !== null) {
      return $model;
    }

    throw new NotFoundHttpException('The requested topic does not exist.');
  }
}
