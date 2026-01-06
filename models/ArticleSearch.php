<?php

namespace app\models;

use yii\data\ActiveDataProvider;

/**
 * ArticleSearch handles filtering and searching for Article records.
 */
class ArticleSearch extends Article
{
  /**
   * Validation rules for search fields.
   */
  public function rules()
  {
    return [
      [['id', 'viewed', 'user_id', 'topic_id', 'upvotes'], 'integer'],
      [['title', 'description', 'date', 'tag'], 'safe'],
    ];
  }

  /**
   * Builds data provider instance with search conditions applied.
   */
  public function search($params)
  {
    // Base query for articles
    $query = Article::find();

    // Data provider with default sorting
    $dataProvider = new ActiveDataProvider([
      'query' => $query,
      'sort' => ['defaultOrder' => ['date' => SORT_DESC]],
    ]);

    // Load incoming search parameters
    $this->load($params);

    // Return unfiltered data if validation fails
    if (!$this->validate()) {
      return $dataProvider;
    }

    // Exact match filters
    $query->andFilterWhere([
      'id' => $this->id,
      'user_id' => $this->user_id,
      'topic_id' => $this->topic_id,
      'date' => $this->date,
    ]);

    // Partial match filters
    $query->andFilterWhere(['like', 'title', $this->title])
      ->andFilterWhere(['like', 'description', $this->description])
      ->andFilterWhere(['like', 'tag', $this->tag]);

    return $dataProvider;
  }
}
