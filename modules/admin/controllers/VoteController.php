<?php

namespace app\modules\admin\controllers;

use Yii;
use app\models\Vote;
use app\models\VoteSearch;
use yii\web\Controller;
use yii\filters\VerbFilter;

class VoteController extends Controller
{
  public function behaviors()
  {
    return ['verbs' => ['class' => VerbFilter::class, 'actions' => ['delete' => ['POST']]]];
  }

  public function actionIndex()
  {
    $searchModel = new VoteSearch();
    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
    return $this->render('index', ['searchModel' => $searchModel, 'dataProvider' => $dataProvider]);
  }

  public function actionDelete($id)
  {
    $vote = Vote::findOne($id);
    if ($vote) {
      // При видаленні лайка з адмінки, треба зменшити лічильник в статті
      $vote->article->updateCounters(['upvotes' => -1]);
      $vote->delete();
    }
    return $this->redirect(['index']);
  }

  public function actionView($id)
  {
    // Vote зазвичай не має окремої моделі пошуку findModel, тому використовуємо прямий пошук
    return $this->render('view', [
      'model' => \app\models\Vote::findOne($id),
    ]);
  }
}
