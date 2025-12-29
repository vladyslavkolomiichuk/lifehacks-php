<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class Vote extends ActiveRecord
{
  public static function tableName()
  {
    return '{{%vote}}';
  }

  public function rules()
  {
    return [
      [['user_id', 'article_id'], 'required'],
      [['user_id', 'article_id'], 'integer'],
    ];
  }
}
