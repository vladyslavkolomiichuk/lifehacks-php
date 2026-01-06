<?php

namespace app\models;

use Yii;

/**
 * ActiveRecord model for the "comment" table.
 *
 * @property int $id
 * @property string|null $text
 * @property int|null $user_id
 * @property int|null $article_id
 * @property int|null $parent_id
 * @property string|null $date
 * @property int|null $delete
 *
 * @property Article $article
 * @property User $user
 * @property Comment $parent
 * @property Comment[] $children
 */
class Comment extends \yii\db\ActiveRecord
{
    /**
     * Returns the table name.
     */
    public static function tableName()
    {
        return 'comment';
    }

    /**
     * Validation rules.
     */
    public function rules()
    {
        return [
            // Default values
            [['text', 'user_id', 'article_id', 'parent_id', 'date', 'delete'], 'default', 'value' => null],

            // Integer fields
            [['user_id', 'article_id', 'parent_id', 'delete', 'is_edited'], 'integer'],

            // Safe date field
            [['date'], 'safe'],

            // Text length limit
            [['text'], 'string', 'max' => 255],

            // Related records existence checks
            [['article_id'], 'exist', 'skipOnError' => true, 'targetClass' => Article::class, 'targetAttribute' => ['article_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
            [['parent_id'], 'exist', 'skipOnError' => true, 'targetClass' => Comment::class, 'targetAttribute' => ['parent_id' => 'id']],
        ];
    }

    /**
     * Attribute labels.
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'text' => 'Text',
            'user_id' => 'User ID',
            'article_id' => 'Article ID',
            'parent_id' => 'Parent Comment',
            'date' => 'Date',
            'delete' => 'Delete',
        ];
    }

    /**
     * Related article.
     */
    public function getArticle()
    {
        return $this->hasOne(Article::class, ['id' => 'article_id']);
    }

    /**
     * Comment author.
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * Child comments (replies).
     */
    public function getChildren()
    {
        return $this->hasMany(Comment::class, ['parent_id' => 'id']);
    }

    /**
     * Parent comment.
     */
    public function getParent()
    {
        return $this->hasOne(Comment::class, ['id' => 'parent_id']);
    }
}
