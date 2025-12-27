<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%article}}`.
 */
class m251227_160355_create_article_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%article}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string(),      // Заголовок поради
            'description' => $this->text(),  // Текст поради
            'date' => $this->date(),         // Дата створення
            'image' => $this->string(),      // Головне фото
            'tag' => $this->string(),        // Теги для пошуку
            'viewed' => $this->integer(),    // Лічильник переглядів
            'topic_id' => $this->integer(),  // Зв'язок з категорією
            'user_id' => $this->integer(),   // Зв'язок з автором
        ]);

        // Створюємо індекс і зовнішній ключ для категорії
        $this->createIndex('idx-topic_id', 'article', 'topic_id');
        $this->addForeignKey('fk-topic_id', 'article', 'topic_id', 'topic', 'id', 'CASCADE');

        // Створюємо індекс і зовнішній ключ для автора
        $this->createIndex('idx-article-user_id', 'article', 'user_id');
        $this->addForeignKey('fk-article-user_id', 'article', 'user_id', 'user', 'id', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Спочатку видаляємо ключі, потім таблицю
        $this->dropForeignKey('fk-topic_id', 'article');
        $this->dropForeignKey('fk-article-user_id', 'article');
        $this->dropTable('{{%article}}');
    }
}
