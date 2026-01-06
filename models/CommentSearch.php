<?php

namespace app\models;

use yii\data\ActiveDataProvider;

/**
 * CommentSearch handles filtering and sorting of comments.
 */
class CommentSearch extends Comment
{
  /**
   * Validation rules for search fields.
   */
  public function rules()
  {
    return [
      [['id', 'user_id', 'article_id'], 'integer'],
      [['text', 'date'], 'safe'],
    ];
  }

  /**
   * Builds data provider with applied search filters.
   */
  public function search($params)
  {
    // Base query
    $query = Comment::find();

    // Data provider with default sorting
    $dataProvider = new ActiveDataProvider([
      'query' => $query,
      'sort' => ['defaultOrder' => ['date' => SORT_DESC]],
    ]);

    // Load search params
    $this->load($params);

    // Return unfiltered data on validation failure
    if (!$this->validate()) {
      return $dataProvider;
    }

    // Exact match filters
    $query->andFilterWhere([
      'id' => $this->id,
      'user_id' => $this->user_id,
      'article_id' => $this->article_id,
    ]);

    // Partial text match
    $query->andFilterWhere(['like', 'text', $this->text]);

    return $dataProvider;
  }
}
