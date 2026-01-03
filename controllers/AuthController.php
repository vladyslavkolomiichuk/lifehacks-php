<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use app\models\LoginForm;
use app\models\SignupForm;
use app\models\User;

class AuthController extends Controller
{
  /**
   * Налаштування доступу
   */
  public function behaviors()
  {
    return [
      'access' => [
        'class' => AccessControl::class,
        'only' => ['logout', 'login', 'signup'],
        'rules' => [
          [
            'actions' => ['login', 'signup'],
            'allow' => true,
            'roles' => ['?'], // Тільки гості
          ],
          [
            'actions' => ['logout'],
            'allow' => true,
            'roles' => ['@'], // Тільки авторизовані
          ],
        ],
      ],
    ];
  }

  /**
   * Вхід
   */
  public function actionLogin()
  {
    if (!Yii::$app->user->isGuest) {
      return $this->goHome();
    }

    $model = new LoginForm();
    if ($model->load(Yii::$app->request->post()) && $model->login()) {
      return $this->goBack();
    }

    $model->password = '';
    return $this->render('login', [
      'model' => $model,
    ]);
  }

  /**
   * Реєстрація
   */
  public function actionSignup()
  {
    $model = new SignupForm();

    if ($model->load(Yii::$app->request->post()) && $model->validate()) {
      $user = new User();
      $user->name = $model->name;
      $user->email = $model->email;
      $user->setPassword($model->password);

      if ($user->save()) {
        return $this->redirect(['login']);
      }
    }

    return $this->render('signup', ['model' => $model]);
  }

  /**
   * Вихід
   */
  public function actionLogout()
  {
    Yii::$app->user->logout();
    return $this->goHome();
  }
}
