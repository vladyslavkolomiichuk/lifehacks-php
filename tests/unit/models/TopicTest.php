<?php

namespace tests\unit\models;

use app\models\Topic;
use app\models\Article;
use app\models\User;

class TopicTest extends \Codeception\Test\Unit
{
  /**
   * @var \UnitTester
   */
  protected $tester;

  protected function _before()
  {
    // Очищаємо таблиці, щоб старі дані не заважали
    Article::deleteAll();
    Topic::deleteAll();
    User::deleteAll();
  }

  // ТЕСТ 1: Перевірка валідації (Rules)
  public function testValidation()
  {
    $topic = new Topic();

    // Сценарій 1: Валідна назва
    $topic->name = 'Programming';
    $this->assertTrue($topic->validate(), 'Topic with normal name should be valid');

    // Сценарій 2: Занадто довга назва (max 255)
    $topic->name = str_repeat('a', 256);
    $this->assertFalse($topic->validate(['name']), 'Name should not exceed 255 chars');

    // Сценарій 3: Перевірка типу даних (String)
    // Yii2 автоматично не конвертує масив у рядок, це має викликати помилку
    $topic->name = ['array', 'is', 'not', 'string'];
    $this->assertFalse($topic->validate(['name']), 'Name must be a string');
  }

  // ТЕСТ 2: Перевірка збереження та зв'язку зі статтями
  public function testSavingAndArticlesRelation()
  {
    // 1. Створюємо Тему
    $topic = new Topic();
    $topic->name = 'Lifehacks';

    // Перевіряємо, чи зберігся запис
    $this->assertTrue($topic->save(), 'Topic should be saved');
    $this->assertNotNull($topic->id, 'Topic should have an ID after save');

    // 2. Підготовка для зв'язку (нам потрібен User для створення статті)
    $user = new User(['name' => 'Author', 'email' => 'a@a.com', 'password' => '123']);
    $user->save(false);

    // 3. Створюємо 2 статті, прив'язані до цієї теми ($topic->id)
    $article1 = new Article([
      'title' => 'Lifehack #1',
      'user_id' => $user->id,
      'topic_id' => $topic->id, // <--- Зв'язок тут
      'date' => date('Y-m-d')
    ]);
    $article1->save(false);

    $article2 = new Article([
      'title' => 'Lifehack #2',
      'user_id' => $user->id,
      'topic_id' => $topic->id, // <--- Зв'язок тут
      'date' => date('Y-m-d')
    ]);
    $article2->save(false);

    // 4. Створюємо статтю в ІНШІЙ темі (для чистоти експерименту)
    $otherTopic = new Topic(['name' => 'Other']);
    $otherTopic->save(false);

    $article3 = new Article([
      'title' => 'Other Article',
      'user_id' => $user->id,
      'topic_id' => $otherTopic->id // Інша тема
    ]);
    $article3->save(false);


    // --- ПЕРЕВІРКА ЗВ'ЯЗКУ ---

    // Оновлюємо модель теми, щоб підтягнути дані
    // (хоча Active Record лінивий, краще звертатись до властивості ->articles)

    $relatedArticles = $topic->articles; // Викликає getArticles()

    // Має бути 2 статті
    $this->assertCount(2, $relatedArticles, 'Topic should have exactly 2 related articles');

    // Перевіряємо назви, щоб переконатись, що це ті самі статті
    $titles = [$relatedArticles[0]->title, $relatedArticles[1]->title];
    $this->assertContains('Lifehack #1', $titles);
    $this->assertContains('Lifehack #2', $titles);
    $this->assertNotContains('Other Article', $titles);
  }
}
