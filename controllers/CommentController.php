<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use app\models\Comment;
use app\models\CommentForm;
use yii\web\Response;

class CommentController extends Controller
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
   * Додати коментар (Залишаємо як є, або теж можна переробити під AJAX при бажанні)
   */
  public function actionCreate($id)
  {
    $model = new CommentForm();

    if (Yii::$app->request->isPost) {
      $model->load(Yii::$app->request->post());
      if ($model->validate()) {
        $comment = new Comment();
        $comment->article_id = $id;
        $comment->user_id = Yii::$app->user->id;
        $comment->text = $model->comment;
        $comment->date = date('Y-m-d');

        if (!empty($model->parentId)) {
          $comment->parent_id = $model->parentId;
        }

        $comment->save();
        Yii::$app->session->setFlash('success', "Comment added");
        return $this->redirect(['article/view', 'id' => $id]);
      }
    }
    return $this->redirect(['article/view', 'id' => $id]);
  }

  /**
   * Видалити коментар (Оновлено для AJAX)
   */
  public function actionDelete($id)
  {
    $comment = Comment::findOne($id);

    // Перевірка прав доступу
    if (!$comment || $comment->user_id != Yii::$app->user->id) {
      if (Yii::$app->request->isAjax) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return ['success' => false, 'message' => 'Access denied'];
      }
      throw new \yii\web\ForbiddenHttpException("Access denied.");
    }

    $comment->delete();

    // ЯКЩО ЦЕ AJAX (запит від JavaScript)
    if (Yii::$app->request->isAjax) {
      Yii::$app->response->format = Response::FORMAT_JSON;
      return ['success' => true];
    }

    // Якщо це звичайний запит - робимо редірект
    Yii::$app->session->setFlash('success', "Comment deleted.");
    return $this->redirect(Yii::$app->request->referrer);
  }

  /**
   * Редагувати коментар
   */
  public function actionUpdate($id)
  {
    $comment = Comment::findOne($id);

    // Перевірка прав
    if (!$comment || $comment->user_id != Yii::$app->user->id) {
      if (Yii::$app->request->isAjax) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return ['success' => false, 'message' => 'Access denied'];
      }
      throw new \yii\web\ForbiddenHttpException("Access denied.");
    }

    $model = new CommentForm();

    // 1. ЛОГІКА ДЛЯ AJAX (те, що працює на сторінці)
    if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
      Yii::$app->response->format = Response::FORMAT_JSON;

      $comment->text = $model->comment;
      $comment->is_edited = 1;

      if ($comment->save()) {
        return [
          'success' => true,
          'text' => \yii\helpers\Html::encode($comment->text),
          'is_edited' => true
        ];
      } else {
        return ['success' => false, 'message' => 'Validation error'];
      }
    }

    // 2. ЛОГІКА ДЛЯ ЗВИЧАЙНОГО ЗАПИТУ (якщо JS вимкнено або відкрили в новому вікні)
    // Замість рендеру окремої сторінки, просто повертаємо назад до статті
    return $this->redirect(['article/view', 'id' => $comment->article_id]);
  }
}
