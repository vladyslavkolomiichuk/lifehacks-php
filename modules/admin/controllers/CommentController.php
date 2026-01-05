<?php

namespace app\modules\admin\controllers;

use Yii;
use app\models\Comment;
use app\models\CommentSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

class CommentController extends Controller
{
  public function behaviors()
  {
    return [
      // 2. Додайте цей блок AccessControl
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
      'verbs' => [
        'class' => VerbFilter::class,
        'actions' => ['delete' => ['POST']],
      ],
    ];
  }

  public function actionIndex()
  {
    $searchModel = new CommentSearch();
    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
    return $this->render('index', ['searchModel' => $searchModel, 'dataProvider' => $dataProvider]);
  }

  public function actionDelete($id)
  {
    $this->findModel($id)->delete();
    return $this->redirect(['index']);
  }

  // Для модерації (змінити текст, якщо там щось погане)
  public function actionUpdate($id)
  {
    $model = $this->findModel($id);
    if ($model->load(Yii::$app->request->post()) && $model->save()) return $this->redirect(['index']);
    return $this->render('update', ['model' => $model]);
  }

  protected function findModel($id)
  {
    if (($model = Comment::findOne($id)) !== null) return $model;
    throw new NotFoundHttpException('Page not found.');
  }

  public function actionView($id)
  {
    return $this->render('view', [
      'model' => $this->findModel($id),
    ]);
  }
}
