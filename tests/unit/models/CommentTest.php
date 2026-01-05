<?php

namespace tests\unit\models;

use app\models\Comment;
use app\models\Article;
use app\models\User;
use app\models\Topic;

class CommentTest extends \Codeception\Test\Unit
{
  /**
   * @var \UnitTester
   */
  protected $tester;

  // Очищаємо базу перед кожним тестом
  protected function _before()
  {
    Comment::deleteAll();
    Article::deleteAll();
    Topic::deleteAll();
    User::deleteAll();
  }

  // ТЕСТ 1: Перевірка правил валідації (Rules)
  public function testValidation()
  {
    $comment = new Comment();

    // Сценарій: Текст занадто довгий (max 255)
    $comment->text = str_repeat('a', 256);
    $this->assertFalse($comment->validate(['text']), 'Text should be max 255 chars');

    // Сценарій: Валідний текст
    $comment->text = 'This is a valid comment';
    $this->assertTrue($comment->validate(['text']));

    // Сценарій: Перевірка цілих чисел
    $comment->user_id = 'not-integer';
    $this->assertFalse($comment->validate(['user_id']), 'User ID must be integer');
  }

  // ТЕСТ 2: Перевірка зв'язків (User, Article) та збереження
  public function testSavingAndBasicRelations()
  {
    // 1. Створюємо залежності (User, Topic, Article), бо Comment вимагає їх існування
    $user = new User(['name' => 'Commenter', 'email' => 'c@test.com', 'password' => '123']);
    $user->save(false);

    $topic = new Topic(['name' => 'General']);
    $topic->save(false);

    $article = new Article([
      'title' => 'Article for comments',
      'user_id' => $user->id,
      'topic_id' => $topic->id
    ]);
    $article->save(false);

    // 2. Створюємо коментар
    $comment = new Comment();
    $comment->text = 'Great article!';
    $comment->user_id = $user->id;
    $comment->article_id = $article->id;
    $comment->date = date('Y-m-d');

    // 3. Перевіряємо, чи він зберігся
    $this->assertTrue($comment->save(), 'Comment should be saved successfully');

    // 4. Перевіряємо зв'язки (Relations)
    $this->assertNotNull($comment->user, 'Should have relation to User');
    $this->assertEquals($user->name, $comment->user->name);

    $this->assertNotNull($comment->article, 'Should have relation to Article');
    $this->assertEquals($article->title, $comment->article->title);
  }

  // ТЕСТ 3: Перевірка ієрархії (Відповіді / Replies)
  public function testParentChildHierarchy()
  {
    // Підготовка даних
    $user = new User(['name' => 'User', 'email' => 'u@t.com', 'password' => '123']);
    $user->save(false);
    $topic = new Topic(['name' => 'T']);
    $topic->save(false);
    $article = new Article(['title' => 'A', 'user_id' => $user->id, 'topic_id' => $topic->id]);
    $article->save(false);

    // 1. Створюємо БАТЬКІВСЬКИЙ коментар (Root)
    $parentComment = new Comment([
      'text' => 'This is the main comment',
      'user_id' => $user->id,
      'article_id' => $article->id
    ]);
    $parentComment->save();

    // 2. Створюємо ВІДПОВІДЬ (Child), вказуючи parent_id
    $childComment = new Comment([
      'text' => 'This is a reply',
      'user_id' => $user->id,
      'article_id' => $article->id,
      'parent_id' => $parentComment->id // <--- Зв'язуємо з батьком
    ]);

    $this->assertTrue($childComment->save(), 'Child comment should save with valid parent_id');

    // 3. Перевіряємо зв'язок "Children" (від батька до дитини)
    // Оновлюємо модель батька з БД, щоб підтягнути зв'язки
    $parentComment->refresh();

    $this->assertNotEmpty($parentComment->children, 'Parent should have children');
    $this->assertEquals(1, count($parentComment->children));
    $this->assertEquals('This is a reply', $parentComment->children[0]->text);

    // 4. Перевіряємо зв'язок "Parent" (від дитини до батька)
    $this->assertNotNull($childComment->parent, 'Child should have a parent');
    $this->assertEquals($parentComment->id, $childComment->parent->id);
  }

  // ТЕСТ 4: Перевірка на неіснуючого батька
  public function testInvalidParent()
  {
    // Підготовка
    $user = new User(['name' => 'User', 'email' => 'u2@t.com', 'password' => '123']);
    $user->save(false);
    $topic = new Topic(['name' => 'T2']);
    $topic->save(false);
    $article = new Article(['title' => 'A2', 'user_id' => $user->id, 'topic_id' => $topic->id]);
    $article->save(false);

    $comment = new Comment([
      'text' => 'Orphan comment',
      'user_id' => $user->id,
      'article_id' => $article->id,
      'parent_id' => 99999 // Такого ID не існує
    ]);

    // Валідація має не пройти, бо у вас в правилах є 'exist' для parent_id
    $this->assertFalse($comment->validate(['parent_id']), 'Should not validate if parent_id does not exist');
  }
}
