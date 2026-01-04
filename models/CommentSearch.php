<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Comment;

class CommentSearch extends Comment
{
  public function rules()
  {
    return [[['id', 'user_id', 'article_id'], 'integer'], [['text', 'date'], 'safe']];
  }
  public function search($params)
  {
    $query = Comment::find();
    $dataProvider = new ActiveDataProvider(['query' => $query, 'sort' => ['defaultOrder' => ['date' => SORT_DESC]]]);
    $this->load($params);
    if (!$this->validate()) return $dataProvider;
    $query->andFilterWhere(['id' => $this->id, 'user_id' => $this->user_id, 'article_id' => $this->article_id]);
    $query->andFilterWhere(['like', 'text', $this->text]);
    return $dataProvider;
  }
}
