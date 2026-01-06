<?php

namespace app\models;

use Yii;

/**
 * ActiveRecord model for the "vote" table.
 *
 * @property int $id
 * @property int $user_id
 * @property int $article_id
 *
 * @property Article $article
 * @property User $user
 */
class Vote extends \yii\db\ActiveRecord
{
  /**
   * Returns the table name.
   */
  public static function tableName()
  {
    return 'vote';
  }

  /**
   * Validation rules.
   */
  public function rules()
  {
    return [
      [['user_id', 'article_id'], 'required'],
      [['user_id', 'article_id'], 'integer'],
    ];
  }

  /**
   * Attribute labels.
   */
  public function attributeLabels()
  {
    return [
      'id' => 'ID',
      'user_id' => 'User',
      'article_id' => 'Article',
    ];
  }

  /**
   * Related user.
   */
  public function getUser()
  {
    return $this->hasOne(User::class, ['id' => 'user_id']);
  }

  /**
   * Related article.
   */
  public function getArticle()
  {
    return $this->hasOne(Article::class, ['id' => 'article_id']);
  }
}
