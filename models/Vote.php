<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "vote".
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
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'vote';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['user_id', 'article_id'], 'required'],
      [['user_id', 'article_id'], 'integer'],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels()
  {
    return [
      'id' => 'ID',
      'user_id' => 'User',
      'article_id' => 'Article',
    ];
  }

    // === ДОДАЙТЕ ЦІ МЕТОДИ ===

  /**
   * Зв'язок з користувачем
   */
  public function getUser()
  {
    return $this->hasOne(User::class, ['id' => 'user_id']);
  }

  /**
   * Зв'язок зі статтею
   */
  public function getArticle()
  {
    return $this->hasOne(Article::class, ['id' => 'article_id']);
  }
}
