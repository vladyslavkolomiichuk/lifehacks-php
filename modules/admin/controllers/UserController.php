<?php

namespace app\modules\admin\controllers;

use Yii;
use app\models\User;
use app\models\UserSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * Admin controller for managing users.
 */
class UserController extends Controller
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
   * Lists all users with search and filter.
   */
  public function actionIndex()
  {
    $searchModel = new UserSearch();
    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

    return $this->render('index', [
      'searchModel' => $searchModel,
      'dataProvider' => $dataProvider,
    ]);
  }

  /**
   * Updates a user.
   */
  public function actionUpdate($id)
  {
    $model = $this->findModel($id);

    if ($model->load(Yii::$app->request->post()) && $model->save()) {
      Yii::$app->session->setFlash('success', 'User updated successfully.');
      return $this->redirect(['index']);
    }

    return $this->render('update', ['model' => $model]);
  }

  /**
   * Deletes a user. Prevents deleting self.
   */
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

  /**
   * Displays a single user.
   */
  public function actionView($id)
  {
    return $this->render('view', [
      'model' => $this->findModel($id),
    ]);
  }

  /**
   * Finds the User model by ID or throws 404.
   */
  protected function findModel($id)
  {
    if (($model = User::findOne($id)) !== null) {
      return $model;
    }

    throw new NotFoundHttpException('The requested page does not exist.');
  }
}
