<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Topic;

class TopicSearch extends Topic
{
  public function rules()
  {
    return [[['id'], 'integer'], [['name'], 'safe']];
  }
  public function search($params)
  {
    $query = Topic::find();
    $dataProvider = new ActiveDataProvider(['query' => $query]);
    $this->load($params);
    if (!$this->validate()) return $dataProvider;
    $query->andFilterWhere(['id' => $this->id])->andFilterWhere(['like', 'name', $this->name]);
    return $dataProvider;
  }
}
