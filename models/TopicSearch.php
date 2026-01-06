<?php

namespace app\models;

use yii\data\ActiveDataProvider;

/**
 * TopicSearch handles filtering of topics.
 */
class TopicSearch extends Topic
{
  /**
   * Validation rules for search fields.
   */
  public function rules()
  {
    return [
      [['id'], 'integer'],
      [['name'], 'safe'],
    ];
  }

  /**
   * Builds data provider with search filters applied.
   */
  public function search($params)
  {
    $query = Topic::find();

    $dataProvider = new ActiveDataProvider([
      'query' => $query,
    ]);

    $this->load($params);

    if (!$this->validate()) {
      return $dataProvider;
    }

    $query
      ->andFilterWhere(['id' => $this->id])
      ->andFilterWhere(['like', 'name', $this->name]);

    return $dataProvider;
  }
}
