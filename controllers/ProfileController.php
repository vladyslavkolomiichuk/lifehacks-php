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
            'roles' => ['@'], // Тільки авторизовані
          ],
        ],
      ],
    ];
  }

  /**
   * Dashboard: Статті користувача та інфо
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
   * Налаштування профілю (Аватар, Пароль)
   */
  public function actionUpdate()
  {
    $user = User::findOne(Yii::$app->user->id);

    // 1. Зберігаємо старий пароль (хеш), який зараз є в базі
    $oldPasswordHash = $user->password;
    // Зберігаємо старе фото, щоб не зникло, якщо не завантажили нове
    $oldImage = $user->image;

    // Очищаємо поле пароля в моделі, щоб у формі воно було пустим
    $user->password = '';

    if ($user->load(Yii::$app->request->post())) {

      // 2. ЛОГІКА ПАРОЛЯ
      // Якщо поле пароля пусте (користувач не хоче його змінювати)
      if (empty($user->password)) {
        // Повертаємо старий хеш
        $user->password = $oldPasswordHash;
      } else {
        // Якщо користувач ввів щось нове -> генеруємо новий хеш
        // Важливо: метод setPassword має бути у вашій моделі User
        $user->setPassword($user->password);
      }

      // 3. ЛОГІКА КАРТИНКИ
      $file = \yii\web\UploadedFile::getInstance($user, 'image');
      if ($file) {
        $filename = strtolower(md5(uniqid($file->baseName))) . '.' . $file->extension;
        $file->saveAs(Yii::getAlias('@webroot') . '/uploads/' . $filename);
        $user->image = $filename;
      } else {
        // Якщо файл не вибрали, залишаємо старий
        $user->image = $oldImage;
      }

      // 4. ЗБЕРЕЖЕННЯ
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
