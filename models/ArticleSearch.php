<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Article;

class ArticleSearch extends Article
{
  public function rules()
  {
    return [
      [['id', 'viewed', 'user_id', 'topic_id', 'upvotes'], 'integer'],
      [['title', 'description', 'date', 'tag'], 'safe'],
    ];
  }

  public function search($params)
  {
    $query = Article::find();
    $dataProvider = new ActiveDataProvider([
      'query' => $query,
      'sort' => ['defaultOrder' => ['date' => SORT_DESC]],
    ]);

    $this->load($params);

    if (!$this->validate()) return $dataProvider;

    $query->andFilterWhere([
      'id' => $this->id,
      'user_id' => $this->user_id,
      'topic_id' => $this->topic_id,
      'date' => $this->date,
    ]);

    $query->andFilterWhere(['like', 'title', $this->title])
      ->andFilterWhere(['like', 'description', $this->description])
      ->andFilterWhere(['like', 'tag', $this->tag]);

    return $dataProvider;
  }
}
