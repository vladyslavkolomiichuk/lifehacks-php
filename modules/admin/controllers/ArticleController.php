<?php

namespace app\modules\admin\controllers;

use Yii;
use app\models\Article;
use app\models\ArticleSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use yii\filters\AccessControl;

/**
 * Admin controller for managing articles.
 */
class ArticleController extends Controller
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
   * Lists all articles with search and filter.
   */
  public function actionIndex()
  {
    $searchModel = new ArticleSearch();
    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

    return $this->render('index', [
      'searchModel' => $searchModel,
      'dataProvider' => $dataProvider,
    ]);
  }

  /**
   * Displays a single article.
   */
  public function actionView($id)
  {
    return $this->render('view', [
      'model' => $this->findModel($id),
    ]);
  }

  /**
   * Creates a new article.
   */
  public function actionCreate()
  {
    $model = new Article();

    if ($model->load(Yii::$app->request->post())) {
      $model->date = date('Y-m-d');
      $model->viewed = 0;
      $model->upvotes = 0;

      // Handle image upload
      $model->image = UploadedFile::getInstance($model, 'image');
      if ($model->image) {
        $filename = strtolower(md5(uniqid($model->image->baseName))) . '.' . $model->image->extension;
        $model->image->saveAs(Yii::getAlias('@webroot') . '/uploads/' . $filename);
        $model->image = $filename;
      }

      if ($model->save()) {
        return $this->redirect(['view', 'id' => $model->id]);
      }
    }

    return $this->render('create', ['model' => $model]);
  }

  /**
   * Updates an existing article.
   */
  public function actionUpdate($id)
  {
    $model = $this->findModel($id);
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
        return $this->redirect(['view', 'id' => $model->id]);
      }
    }

    return $this->render('update', ['model' => $model]);
  }

  /**
   * Deletes an article.
   */
  public function actionDelete($id)
  {
    $this->findModel($id)->delete();
    return $this->redirect(['index']);
  }

  /**
   * Finds the Article model by ID or throws 404.
   */
  protected function findModel($id)
  {
    if (($model = Article::findOne($id)) !== null) {
      return $model;
    }

    throw new NotFoundHttpException('The requested page does not exist.');
  }
}
