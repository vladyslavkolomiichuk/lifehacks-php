<?php

namespace tests\unit\models;

use Yii;
use app\models\CommentForm;
use app\models\User;
use app\models\Article;
use app\models\Topic;
use app\models\Comment;

class CommentFormTest extends \Codeception\Test\Unit
{
  /**
   * @var \UnitTester
   */
  protected $tester;

  protected function _before()
  {
    // Очищаємо таблиці
    Comment::deleteAll();
    Article::deleteAll();
    Topic::deleteAll();
    User::deleteAll();
  }

  // ТЕСТ 1: Перевірка валідації форми
  public function testValidation()
  {
    $form = new CommentForm();

    // Сценарій 1: Порожній коментар (Required)
    $form->comment = null;
    $this->assertFalse($form->validate(['comment']), 'Comment is required');

    // Сценарій 2: Занадто короткий (min 3)
    $form->comment = 'Hi';
    $this->assertFalse($form->validate(['comment']), 'Comment is too short');

    // Сценарій 3: Занадто довгий (max 250)
    $form->comment = str_repeat('a', 251);
    $this->assertFalse($form->validate(['comment']), 'Comment is too long');

    // Сценарій 4: Правильний коментар
    $form->comment = 'Good article!';
    $this->assertTrue($form->validate(['comment']));

    // Сценарій 5: Перевірка parentId (має бути integer)
    $form->parentId = 'abc';
    $this->assertFalse($form->validate(['parentId']), 'Parent ID must be integer');
  }

  // ТЕСТ 2: Перевірка збереження (saveComment)
  public function testSaveComment()
  {
    // 1. Підготовка даних (Юзер, Топік, Стаття)
    $user = new User(['name' => 'Tester', 'email' => 't@t.com', 'password' => '123']);
    $user->save(false);

    $topic = new Topic(['name' => 'Tech']);
    $topic->save(false);

    $article = new Article(['title' => 'News', 'user_id' => $user->id, 'topic_id' => $topic->id]);
    $article->save(false);

    // 2. ВАЖЛИВО: Логінимо користувача, бо saveComment бере Yii::$app->user->id
    Yii::$app->user->login($user);

    // 3. Заповнюємо форму
    $form = new CommentForm();
    $form->comment = 'This is a test comment';
    // $form->parentId = 5; // Поки що ваш код це ігнорує (див. примітку нижче)

    // 4. Викликаємо метод збереження
    $result = $form->saveComment($article->id);

    // 5. Перевірки
    $this->assertTrue($result, 'saveComment should return true');

    // Перевіряємо, чи з'явився запис у БД
    $savedComment = Comment::findOne(['text' => 'This is a test comment']);
    $this->assertNotNull($savedComment, 'Comment should exist in DB');

    $this->assertEquals($user->id, $savedComment->user_id, 'User ID should match logged user');
    $this->assertEquals($article->id, $savedComment->article_id, 'Article ID should match');
    $this->assertEquals(date('Y-m-d'), $savedComment->date, 'Date should be today');
  }
}
