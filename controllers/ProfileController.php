<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use app\models\User;
use app\models\ImageUpload; // Використаємо, якщо є, або напишемо логіку тут
use yii\web\UploadedFile;
use app\models\Article;
use app\models\Topic;

class ProfileController extends Controller
{
  /**
   * Доступ тільки для авторизованих користувачів
   */
  public function behaviors()
  {
    return [
      'access' => [
        'class' => AccessControl::class,
        'rules' => [
          [
            'allow' => true,
            'roles' => ['@'], // @ = авторизовані
          ],
        ],
      ],
    ];
  }

  /**
   * Головна сторінка кабінету (Dashboard)
   */
  public function actionIndex()
  {
    // Отримуємо поточного користувача з бази даних
    $user = User::findOne(Yii::$app->user->id);

    // Отримуємо статті цього користувача
    $articles = $user->articles;

    return $this->render('index', [
      'user' => $user,
      'articles' => $articles,
    ]);
  }

  /**
   * Редагування профілю
   */
  public function actionUpdate()
  {
    $user = User::findOne(Yii::$app->user->id);

    // Зберігаємо старий пароль, щоб не затерти його, якщо поле пусте
    $oldPassword = $user->password;

    if ($user->load(Yii::$app->request->post())) {

      // Логіка завантаження аватарки
      $file = UploadedFile::getInstance($user, 'image');
      if ($file) {
        // Генеруємо унікальне ім'я
        $filename = strtolower(md5(uniqid($file->baseName))) . '.' . $file->extension;
        $file->saveAs(Yii::getAlias('@webroot') . '/uploads/' . $filename);
        $user->image = $filename;
      } else {
        // Якщо файл не завантажували, залишаємо старий (Yii може затерти його null-ом при load)
        $user->image = $user->getOldAttribute('image');
      }

      // Логіка пароля: якщо поле пусте, залишаємо старий
      if (empty($user->password)) {
        $user->password = $oldPassword;
      }

      if ($user->save()) {
        Yii::$app->session->setFlash('success', 'Profile updated successfully!');
        return $this->redirect(['index']);
      }
    }

    // Очищаємо поле пароля перед виводом форми (безпека)
    $user->password = '';

    return $this->render('update', [
      'user' => $user,
    ]);
  }

  public function actionCreateArticle()
  {
    $model = new Article();

    if ($model->load(Yii::$app->request->post())) {

      // Автоматично заповнюємо поля, які не вводить юзер
      $model->user_id = Yii::$app->user->id;
      $model->date = date('Y-m-d');
      $model->viewed = 0;

      // Логіка завантаження картинки
      $model->image = UploadedFile::getInstance($model, 'image');
      if ($model->image) {
        // Генеруємо ім'я
        $filename = strtolower(md5(uniqid($model->image->baseName))) . '.' . $model->image->extension;
        // Зберігаємо файл
        $model->image->saveAs(Yii::getAlias('@webroot') . '/uploads/' . $filename);
        // Записуємо ім'я файлу в БД
        $model->image = $filename;
      }

      if ($model->save()) {
        Yii::$app->session->setFlash('success', 'Article created successfully!');
        return $this->redirect(['index']); // Повертаємось в кабінет
      }
    }

    return $this->render('create-article', [
      'model' => $model,
      'topics' => Topic::find()->all(), // Для випадаючого списку категорій
    ]);
  }

  public function actionUpdateArticle($id)
  {
    $model = Article::findOne($id);

    // ПЕРЕВІРКА ПРАВ: Чи це стаття поточного користувача?
    if (!$model || $model->user_id != Yii::$app->user->id) {
      throw new \yii\web\ForbiddenHttpException("You cannot edit this article.");
    }

    // Зберігаємо стару картинку
    $oldImage = $model->image;

    if ($model->load(Yii::$app->request->post())) {

      // Перевіряємо чи завантажили нову картинку
      $image = UploadedFile::getInstance($model, 'image');
      if ($image) {
        $filename = strtolower(md5(uniqid($image->baseName))) . '.' . $image->extension;
        $image->saveAs(Yii::getAlias('@webroot') . '/uploads/' . $filename);
        $model->image = $filename;
      } else {
        // Якщо нову не завантажили, залишаємо стару
        $model->image = $oldImage;
      }

      // Дата оновлення? Можна залишити дату створення або оновити
      // $model->date = date('Y-m-d'); 

      if ($model->save()) {
        Yii::$app->session->setFlash('success', 'Article updated successfully!');
        return $this->redirect(['index']);
      }
    }

    return $this->render('update-article', [
      'model' => $model,
      'topics' => Topic::find()->all(),
    ]);
  }

  public function actionDeleteArticle($id)
  {
    $model = Article::findOne($id);

    if ($model && $model->user_id == Yii::$app->user->id) {
      // Видаляємо файл картинки з диску (опціонально)
      // if ($model->image && file_exists(Yii::getAlias('@webroot') . '/uploads/' . $model->image)) {
      //    unlink(Yii::getAlias('@webroot') . '/uploads/' . $model->image);
      // }

      $model->delete();
      Yii::$app->session->setFlash('success', 'Article deleted.');
    } else {
      Yii::$app->session->setFlash('error', 'Cannot delete this article.');
    }

    return $this->redirect(['index']);
  }
}
