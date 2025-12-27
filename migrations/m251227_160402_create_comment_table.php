<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%comment}}`.
 */
class m251227_160402_create_comment_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%comment}}', [
            'id' => $this->primaryKey(),
            'text' => $this->string(),
            'user_id' => $this->integer(),    // Хто написав
            'article_id' => $this->integer(), // До якої статті
            'comment_id' => $this->integer(), // ID батьківського коментаря (для відповідей)
            'date' => $this->date(),
            'delete' => $this->boolean(),     // Для "м'якого" видалення (щоб не ламати гілку відповідей)
        ]);

        // Зовнішній ключ на користувача
        $this->createIndex('idx-comment-user_id', 'comment', 'user_id');
        $this->addForeignKey('fk-comment-user_id', 'comment', 'user_id', 'user', 'id', 'CASCADE');

        // Зовнішній ключ на статтю
        $this->createIndex('idx-article_id', 'comment', 'article_id');
        $this->addForeignKey('fk-article_id', 'comment', 'article_id', 'article', 'id', 'CASCADE');

        // Самопосилання (для відповідей на коментарі)
        // Якщо comment_id заповнений, це відповідь на інший коментар
        $this->createIndex('idx-comment_id', 'comment', 'comment_id');
        $this->addForeignKey('fk-comment_id', 'comment', 'comment_id', 'comment', 'id', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-comment-user_id', 'comment');
        $this->dropForeignKey('fk-article_id', 'comment');
        $this->dropForeignKey('fk-comment_id', 'comment');
        $this->dropTable('{{%comment}}');
    }
}
