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
            'roles' => ['@'],
          ],
        ],
      ],
    ];
  }

  /**
   * Add comment
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
   * Delete comment
   */
  public function actionDelete($id)
  {
    $comment = Comment::findOne($id);

    if (!$comment || $comment->user_id != Yii::$app->user->id) {
      if (Yii::$app->request->isAjax) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return ['success' => false, 'message' => 'Access denied'];
      }
      throw new \yii\web\ForbiddenHttpException("Access denied.");
    }

    $comment->delete();

    if (Yii::$app->request->isAjax) {
      Yii::$app->response->format = Response::FORMAT_JSON;
      return ['success' => true];
    }

    Yii::$app->session->setFlash('success', "Comment deleted.");
    return $this->redirect(Yii::$app->request->referrer);
  }

  /**
   * Update comment
   */
  public function actionUpdate($id)
  {
    $comment = Comment::findOne($id);

    if (!$comment || $comment->user_id != Yii::$app->user->id) {
      if (Yii::$app->request->isAjax) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return ['success' => false, 'message' => 'Access denied'];
      }
      throw new \yii\web\ForbiddenHttpException("Access denied.");
    }

    $model = new CommentForm();

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

    return $this->redirect(['article/view', 'id' => $comment->article_id]);
  }
}
