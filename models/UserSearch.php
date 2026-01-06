<?php

namespace app\models;

use yii\data\ActiveDataProvider;
use yii\base\Model;

/**
 * UserSearch handles filtering of users.
 */
class UserSearch extends User
{
    /**
     * Validation rules for search fields.
     */
    public function rules()
    {
        return [
            [['id', 'isAdmin'], 'integer'],
            [['name', 'email'], 'safe'],
        ];
    }

    /**
     * Scenarios (default from Model).
     */
    public function scenarios()
    {
        return Model::scenarios();
    }

    /**
     * Builds data provider with applied search filters.
     */
    public function search($params)
    {
        $query = User::find();

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
            'isAdmin' => $this->isAdmin,
        ]);

        // Partial match filters
        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'email', $this->email]);

        return $dataProvider;
    }
}
