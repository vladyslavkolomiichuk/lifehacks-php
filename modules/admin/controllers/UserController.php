<?php

namespace app\modules\admin\controllers;

use Yii;
use app\models\User;
use app\models\UserSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

class UserController extends Controller
{
  public function behaviors()
  {
    return [
      // 2. Додаємо AccessControl
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
    $searchModel = new UserSearch();
    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

    return $this->render('index', [
      'searchModel' => $searchModel,
      'dataProvider' => $dataProvider,
    ]);
  }

  public function actionUpdate($id)
  {
    $model = $this->findModel($id);
    // We only want to update isAdmin status and maybe Name/Email, not password here usually
    if ($model->load(Yii::$app->request->post()) && $model->save()) {
      Yii::$app->session->setFlash('success', 'User updated successfully.');
      return $this->redirect(['index']);
    }
    return $this->render('update', ['model' => $model]);
  }

  public function actionDelete($id)
  {
    $model = $this->findModel($id);
    if ($model->id == Yii::$app->user->id) {
      Yii::$app->session->setFlash('error', 'You cannot delete yourself!');
    } else {
      $model->delete();
      Yii::$app->session->setFlash('success', 'User deleted.');
    }
    return $this->redirect(['index']);
  }

  protected function findModel($id)
  {
    if (($model = User::findOne($id)) !== null) return $model;
    throw new NotFoundHttpException('The requested page does not exist.');
  }

  public function actionView($id)
  {
    return $this->render('view', [
      'model' => $this->findModel($id),
    ]);
  }
}
