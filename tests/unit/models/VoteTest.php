<?php

namespace tests\unit\models;

use app\models\Vote;
use app\models\User;
use app\models\Article;
use app\models\Topic;

class VoteTest extends \Codeception\Test\Unit
{
  /**
   * @var \UnitTester
   */
  protected $tester;

  protected function _before()
  {
    // Очищаємо всі таблиці перед тестом
    Vote::deleteAll();
    Article::deleteAll();
    Topic::deleteAll();
    User::deleteAll();
  }

  // ТЕСТ 1: Валідація (чи обов'язкові поля)
  public function testValidation()
  {
    $vote = new Vote();

    // Сценарій 1: Порожня модель
    $this->assertFalse($vote->validate(), 'Vote should not be valid without user_id and article_id');
    $this->assertArrayHasKey('user_id', $vote->errors);
    $this->assertArrayHasKey('article_id', $vote->errors);

    // Сценарій 2: Неправильні типи даних (мають бути integer)
    $vote->user_id = 'not-a-number';
    $vote->article_id = 'string';
    $this->assertFalse($vote->validate(), 'IDs must be integers');
  }

  // ТЕСТ 2: Збереження та Зв'язки
  public function testSavingAndRelations()
  {
    // 1. Створюємо "Автора" статті
    $author = new User(['name' => 'Author', 'email' => 'author@test.com', 'password' => '123']);
    $author->save(false);

    // 2. Створюємо "Вортера" (той, хто лайкає)
    $voter = new User(['name' => 'Voter', 'email' => 'voter@test.com', 'password' => '123']);
    $voter->save(false);

    // 3. Створюємо Тему і Статтю
    $topic = new Topic(['name' => 'Tech']);
    $topic->save(false);

    $article = new Article([
      'title' => 'Best Article',
      'user_id' => $author->id,
      'topic_id' => $topic->id
    ]);
    $article->save(false);

    // 4. Створюємо ЛАЙК (Vote)
    $vote = new Vote();
    $vote->user_id = $voter->id;
    $vote->article_id = $article->id;

    // Перевіряємо збереження
    $this->assertTrue($vote->save(), 'Vote should be saved successfully');

    // 5. Перевіряємо зв'язки
    // Чи можемо отримати статтю?
    $this->assertNotNull($vote->article, 'Should perform hasOne relation to Article');
    $this->assertEquals($article->title, $vote->article->title);

    // Чи можемо отримати юзера (того, хто лайкнув)?
    $this->assertNotNull($vote->user, 'Should perform hasOne relation to User');
    $this->assertEquals($voter->email, $vote->user->email);
  }
}
