<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "comment".
 *
 * @property int $id
 * @property string|null $text
 * @property int|null $user_id
 * @property int|null $article_id
 * @property int|null $parent_id   <-- ВИПРАВЛЕНО: Було comment_id
 * @property string|null $date
 * @property int|null $delete
 *
 * @property Article $article
 * @property User $user
 * @property Comment $parent       <-- Батьківський коментар
 * @property Comment[] $children   <-- Дочірні коментарі (відповіді)
 */
class Comment extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'comment';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            // Задаємо значення за замовчуванням null
            [['text', 'user_id', 'article_id', 'parent_id', 'date', 'delete'], 'default', 'value' => null],

            // Вказуємо, що це цілі числа
            [['user_id', 'article_id', 'parent_id', 'delete'], 'integer'],

            [['date'], 'safe'],
            [['text'], 'string', 'max' => 255],

            // Перевірка існування зв'язаних записів
            [['article_id'], 'exist', 'skipOnError' => true, 'targetClass' => Article::class, 'targetAttribute' => ['article_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],

            // Валідація parent_id (перевіряємо, чи існує такий батьківський коментар)
            [['parent_id'], 'exist', 'skipOnError' => true, 'targetClass' => Comment::class, 'targetAttribute' => ['parent_id' => 'id']],

            [['is_edited'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'text' => 'Text',
            'user_id' => 'User ID',
            'article_id' => 'Article ID',
            'parent_id' => 'Parent Comment', // Виправлено назву
            'date' => 'Date',
            'delete' => 'Delete',
        ];
    }

    /**
     * Зв'язок зі статтею
     */
    public function getArticle()
    {
        return $this->hasOne(Article::class, ['id' => 'article_id']);
    }

    /**
     * Зв'язок з автором коментаря
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * ОТРИМАТИ ВІДПОВІДІ (Дітей)
     * Саме цей метод ми використовуємо у views/site/single.php ($comment->children)
     */
    public function getChildren()
    {
        return $this->hasMany(Comment::class, ['parent_id' => 'id']);
    }

    /**
     * Отримати батьківський коментар (опціонально, може знадобитися в майбутньому)
     */
    public function getParent()
    {
        return $this->hasOne(Comment::class, ['id' => 'parent_id']);
    }
}
