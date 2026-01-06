<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\data\Pagination;
use yii\web\UploadedFile;
use yii\web\NotFoundHttpException;
use yii\web\Cookie;

use app\models\Article;
use app\models\Topic;
use app\models\CommentForm;
use app\models\Vote;

class ArticleController extends Controller
{
  public function behaviors()
  {
    return [
      'access' => [
        'class' => AccessControl::class,
        'only' => ['create', 'update', 'delete', 'like'],
        'rules' => [
          [
            'actions' => ['create', 'update', 'delete', 'like'],
            'allow' => true,
            'roles' => ['@'],
          ],
        ],
      ],
    ];
  }

  /**
   * Main articles feed (previously in SiteController::actionIndex)
   */
  public function actionIndex()
  {
    $query = Article::find();
    $count = $query->count();
    $pagination = new Pagination(['totalCount' => $count, 'pageSize' => 5]);

    $articles = $query->orderBy(['date' => SORT_DESC])
      ->offset($pagination->offset)
      ->limit($pagination->limit)
      ->all();

    return $this->render('index', [
      'articles' => $articles,
      'pagination' => $pagination,
      'popular' => $this->getPopular(),
      'recent' => $this->getRecent(),
      'topics' => Topic::find()->all(),
    ]);
  }

  /**
   * Single article view
   */
  public function actionView($id)
  {
    $article = Article::findOne($id);
    if (!$article) {
      throw new NotFoundHttpException("Article not found.");
    }

    // View counter + Cookie
    $cookieName = 'viewed_article_' . $id;
    if (!Yii::$app->request->cookies->has($cookieName)) {
      $article->updateCounters(['viewed' => 1]);
      Yii::$app->response->cookies->add(new Cookie([
        'name' => $cookieName,
        'value' => '1',
        'expire' => time() + 86400,
      ]));
    }

    $comments = $article->getComments()->all();
    $commentForm = new CommentForm();

    return $this->render('view', [
      'article' => $article,
      'comments' => $comments,
      'commentForm' => $commentForm,
      'popular' => $this->getPopular(),
      'topics' => Topic::find()->all(),
    ]);
  }

  /**
   * Create article
   */
  public function actionCreate()
  {
    $model = new Article();

    if ($model->load(Yii::$app->request->post())) {
      $model->user_id = Yii::$app->user->id;
      $model->date = date('Y-m-d');
      $model->viewed = 0;

      $model->image = UploadedFile::getInstance($model, 'image');
      if ($model->image) {
        $filename = strtolower(md5(uniqid($model->image->baseName))) . '.' . $model->image->extension;
        $model->image->saveAs(Yii::getAlias('@webroot') . '/uploads/' . $filename);
        $model->image = $filename;
      }

      if ($model->save()) {
        Yii::$app->session->setFlash('success', 'Article created successfully!');
        return $this->redirect(['profile/index']);
      }
    }

    return $this->render('create', [
      'model' => $model,
      'topics' => Topic::find()->all(),
    ]);
  }

  /**
   * Update article
   */
  public function actionUpdate($id)
  {
    $model = Article::findOne($id);

    if (!$model || $model->user_id != Yii::$app->user->id) {
      throw new \yii\web\ForbiddenHttpException("You cannot edit this article.");
    }

    $oldImage = $model->image;

    if ($model->load(Yii::$app->request->post())) {
      $image = UploadedFile::getInstance($model, 'image');
      if ($image) {
        $filename = strtolower(md5(uniqid($image->baseName))) . '.' . $image->extension;
        $image->saveAs(Yii::getAlias('@webroot') . '/uploads/' . $filename);
        $model->image = $filename;
      } else {
        $model->image = $oldImage;
      }

      if ($model->save()) {
        Yii::$app->session->setFlash('success', 'Article updated!');
        return $this->redirect(['profile/index']);
      }
    }

    return $this->render('update', [
      'model' => $model,
      'topics' => Topic::find()->all(),
    ]);
  }

  /**
   * Delete article
   */
  public function actionDelete($id)
  {
    $model = Article::findOne($id);
    if ($model && $model->user_id == Yii::$app->user->id) {
      $model->delete();
      Yii::$app->session->setFlash('success', 'Article deleted.');
    } else {
      Yii::$app->session->setFlash('error', 'Cannot delete this article.');
    }
    return $this->redirect(['profile/index']);
  }

  /**
   * Categories
   */
  public function actionTopic($id)
  {
    $topic = Topic::findOne($id);
    if (!$topic) throw new NotFoundHttpException("Category not found.");

    $query = Article::find()->where(['topic_id' => $id]);
    $count = $query->count();
    $pagination = new Pagination(['totalCount' => $count, 'pageSize' => 5]);

    $articles = $query->orderBy(['date' => SORT_DESC])
      ->offset($pagination->offset)
      ->limit($pagination->limit)
      ->all();

    return $this->render('topic', [
      'topic' => $topic,
      'articles' => $articles,
      'pagination' => $pagination,
      'popular' => $this->getPopular(),
      'recent' => $this->getRecent(),
      'topics' => Topic::find()->all(),
    ]);
  }

  public function actionSearch($q)
  {
    $query = Article::find();

    $query->where(['like', 'title', $q])
      ->orWhere(['like', 'tag', $q]);

    $pagination = new Pagination([
      'defaultPageSize' => 6,
      'totalCount' => $query->count(),
    ]);

    $articles = $query->offset($pagination->offset)
      ->limit($pagination->limit)
      ->all();

    $popular = Article::find()->orderBy('viewed DESC')->limit(3)->all();
    $topics = \app\models\Topic::find()->all();

    return $this->render('search', [
      'articles' => $articles,
      'pagination' => $pagination,
      'q' => $q,
      'popular' => $popular,
      'topics' => $topics,
    ]);
  }

  /**
   * Likes
   */
  public function actionLike($id)
  {
    $article = Article::findOne($id);
    if (!$article) return $this->redirect(['index']);

    $currentUser = Yii::$app->user->id;
    $vote = Vote::find()->where(['user_id' => $currentUser, 'article_id' => $id])->one();

    if ($vote) {
      $vote->delete();
      $article->updateCounters(['upvotes' => -1]);
    } else {
      $vote = new Vote();
      $vote->user_id = $currentUser;
      $vote->article_id = $id;
      $vote->save();
      $article->updateCounters(['upvotes' => 1]);
    }
    return $this->redirect(Yii::$app->request->referrer);
  }

  // Sidebar helper methods
  protected function getPopular()
  {
    return Article::find()->orderBy(['upvotes' => SORT_DESC])->limit(3)->all();
  }
  protected function getRecent()
  {
    return Article::find()->orderBy(['date' => SORT_DESC])->limit(3)->all();
  }
}
