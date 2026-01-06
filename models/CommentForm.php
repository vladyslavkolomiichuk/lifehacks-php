<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * Form model for creating comments.
 */
class CommentForm extends Model
{
  public $comment;
  public $parentId;

  /**
   * Validation rules.
   */
  public function rules()
  {
    return [
      [['comment'], 'required'],
      [['comment'], 'string', 'length' => [3, 250]],
      [['parentId'], 'integer'],
    ];
  }

  /**
   * Saves a new comment for the given article.
   */
  public function saveComment($article_id)
  {
    $comment = new Comment();
    $comment->text = $this->comment;
    $comment->user_id = Yii::$app->user->id;
    $comment->article_id = $article_id;
    $comment->parent_id = $this->parentId;
    $comment->date = date('Y-m-d');
    $comment->delete = 0;

    return $comment->save();
  }
}
