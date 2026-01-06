<?php

namespace app\models;

use yii\data\ActiveDataProvider;

/**
 * VoteSearch handles filtering of votes.
 */
class VoteSearch extends Vote
{
  /**
   * Validation rules for search fields.
   */
  public function rules()
  {
    return [
      [['id', 'user_id', 'article_id'], 'integer'],
    ];
  }

  /**
   * Builds data provider with applied search filters.
   */
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

    // Exact match filters
    $query->andFilterWhere([
      'id' => $this->id,
      'user_id' => $this->user_id,
      'article_id' => $this->article_id,
    ]);

    return $dataProvider;
  }
}
