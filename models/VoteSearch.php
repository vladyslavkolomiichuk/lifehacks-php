<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Vote;

class VoteSearch extends Vote
{
  public function rules()
  {
    // Переконайтеся, що 'id' є в цьому списку
    return [
      [['id', 'user_id', 'article_id'], 'integer'],
    ];
  }

  public function search($params)
  {
    $query = Vote::find();

    $dataProvider = new ActiveDataProvider([
      'query' => $query,
    ]);

    $this->load($params);

    if (!$this->validate()) {
      return $dataProvider;
    }

    // Фільтрація по ID
    $query->andFilterWhere([
      'id' => $this->id,
      'user_id' => $this->user_id,
      'article_id' => $this->article_id,
    ]);

    return $dataProvider;
  }
}
