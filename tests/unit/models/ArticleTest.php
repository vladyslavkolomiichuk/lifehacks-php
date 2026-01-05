<?php

namespace tests\unit\models;

use app\models\Article;
use app\models\Topic;
use app\models\User;

class ArticleTest extends \Codeception\Test\Unit
{
  /**
   * @var \UnitTester
   */
  protected $tester;

  protected function _before()
  {
    // Очищаємо таблиці перед тестом
    Article::deleteAll();
    Topic::deleteAll();
    User::deleteAll();
  }

  public function testValidation()
  {
    // 1. ПІДГОТОВКА: Створюємо реальні записи в БД для правил 'exist'
    $user = new User(['name' => 'Tester', 'email' => 'test@test.com', 'password' => '123456']);
    $user->save(false);

    $topic = new Topic(['name' => 'Test Topic']);
    $topic->save(false);

    $article = new Article();

    // Сценарій 1: Валідні дані
    $article->title = 'Test Title';
    $article->description = 'Some long text description';
    $article->date = date('Y-m-d');
    $article->user_id = $user->id;   // Використовуємо реальний ID
    $article->topic_id = $topic->id; // Використовуємо реальний ID

    $this->assertTrue($article->validate(), 'Model should be valid with real user/topic IDs');

    // Сценарій 2: Неіснуючий user_id (має бути помилка через правило 'exist')
    $article->user_id = 9999;
    $this->assertFalse($article->validate(['user_id']), 'Should be invalid if user does not exist');

    // Сценарій 3: Занадто довгий заголовок
    $article->user_id = $user->id; // повертаємо валідний ID
    $article->title = str_repeat('a', 256);
    $this->assertFalse($article->validate(['title']), 'Title max 255 chars');
  }

  public function testSavingAndRelations()
  {
    // 1. Створюємо допоміжні дані (User + Topic)
    $user = new User(['name' => 'Author', 'email' => 'author@test.com', 'password' => '123']);
    $user->save(false);

    $topic = new Topic(['name' => 'Tech']);
    $topic->save(false);

    // 2. Створюємо статтю
    $article = new Article();
    $article->title = 'My Article';
    $article->user_id = $user->id;
    $article->topic_id = $topic->id;
    $article->date = date('Y-m-d');

    $this->assertTrue($article->save(), 'Article should be saved');

    // 3. Перевіряємо, чи зберігся ID
    $this->assertNotNull($article->id);
  }

  public function testGetThumb()
  {
    $article = new Article();
    $thumb = $article->getImage();
    $this->assertNotEmpty($thumb);
    // Перевіряємо, чи повертається хоча б рядок (плейсхолдер або шлях)
    $this->assertIsString($thumb);
  }
}
