<?php

namespace app\modules\admin\controllers;

use Yii;
use app\models\Topic;
use app\models\TopicSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

class TopicController extends Controller
{
  public function behaviors()
  {
    return ['verbs' => ['class' => VerbFilter::class, 'actions' => ['delete' => ['POST']]]];
  }
  public function actionIndex()
  {
    $searchModel = new TopicSearch();
    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
    return $this->render('index', ['searchModel' => $searchModel, 'dataProvider' => $dataProvider]);
  }
  public function actionCreate()
  {
    $model = new Topic();
    if ($model->load(Yii::$app->request->post()) && $model->save()) return $this->redirect(['index']);
    return $this->render('create', ['model' => $model]);
  }
  public function actionUpdate($id)
  {
    $model = Topic::findOne($id);
    if ($model->load(Yii::$app->request->post()) && $model->save()) return $this->redirect(['index']);
    return $this->render('update', ['model' => $model]);
  }
  public function actionDelete($id)
  {
    Topic::findOne($id)->delete();
    return $this->redirect(['index']);
  }
  public function actionView($id)
  {
    return $this->render('view', [
      'model' => Topic::findOne($id),
    ]);
  }
}
