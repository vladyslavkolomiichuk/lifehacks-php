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

class ArticleController extends Controller
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
            'matchCallback' => function ($rule, $action) {
              // Перевірка, чи юзер є адміном (поле isAdmin в БД)
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
    $searchModel = new ArticleSearch();
    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

    return $this->render('index', [
      'searchModel' => $searchModel,
      'dataProvider' => $dataProvider,
    ]);
  }

  public function actionView($id)
  {
    return $this->render('view', [
      'model' => $this->findModel($id),
    ]);
  }

  public function actionCreate()
  {
    $model = new Article();

    if ($model->load(Yii::$app->request->post())) {
      $model->date = date('Y-m-d');
      $model->viewed = 0;
      $model->upvotes = 0;

      // Image Upload Logic
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

  public function actionDelete($id)
  {
    $this->findModel($id)->delete();
    return $this->redirect(['index']);
  }

  protected function findModel($id)
  {
    if (($model = Article::findOne($id)) !== null) return $model;
    throw new NotFoundHttpException('The requested page does not exist.');
  }
}
