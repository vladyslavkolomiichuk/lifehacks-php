<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use app\models\User;
use yii\web\UploadedFile;

class ProfileController extends Controller
{
  public function behaviors()
  {
    return [
      'access' => [
        'class' => AccessControl::class,
        'rules' => [
          [
            'allow' => true,
            'roles' => ['@'],
          ],
        ],
      ],
    ];
  }

  /**
   * Dashboard
   */
  public function actionIndex()
  {
    $user = User::findOne(Yii::$app->user->id);
    $articles = $user->articles;

    return $this->render('index', [
      'user' => $user,
      'articles' => $articles,
    ]);
  }

  /**
   * Profile settings
   */
  public function actionUpdate()
  {
    $user = User::findOne(Yii::$app->user->id);

    $oldPasswordHash = $user->password;
    $oldImage = $user->image;

    $user->password = '';

    if ($user->load(Yii::$app->request->post())) {

      if (empty($user->password)) {
        $user->password = $oldPasswordHash;
      } else {
        $user->setPassword($user->password);
      }

      $file = \yii\web\UploadedFile::getInstance($user, 'image');
      if ($file) {
        $filename = strtolower(md5(uniqid($file->baseName))) . '.' . $file->extension;
        $file->saveAs(Yii::getAlias('@webroot') . '/uploads/' . $filename);
        $user->image = $filename;
      } else {
        $user->image = $oldImage;
      }

      if ($user->save()) {
        Yii::$app->session->setFlash('success', 'Profile updated successfully!');
        return $this->redirect(['index']);
      }
    }

    return $this->render('update', [
      'user' => $user,
    ]);
  }
}
