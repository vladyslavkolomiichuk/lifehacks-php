<?php

namespace app\models;

use Yii;
use yii\base\Model;

class CommentForm extends Model
{
  public $comment;
  public $parentId;

  public function rules()
  {
    return [
      [['comment'], 'required'],
      [['comment'], 'string', 'length' => [3, 250]],
      [['parentId'], 'integer'],
    ];
  }

  public function saveComment($article_id)
  {
    $comment = new Comment();
    $comment->text = $this->comment;
    $comment->user_id = Yii::$app->user->id; // Поточний юзер
    $comment->article_id = $article_id;
    $comment->parent_id = $this->parentId;
    $comment->date = date('Y-m-d');
    $comment->delete = 0;
    return $comment->save();
  }
}
