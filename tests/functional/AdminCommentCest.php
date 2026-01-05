<?php

use app\models\User;
use app\models\Article;
use app\models\Topic;
use app\models\Comment;

class AdminCommentCest
{
  private $adminId;
  private $regularUserId;
  private $commentId;

  public function _before(FunctionalTester $I)
  {
    // Повне очищення для стабільності тестів
    Comment::deleteAll();
    Article::deleteAll();
    Topic::deleteAll();
    User::deleteAll();

    // Створюємо Адміна
    $admin = new User(['name' => 'Admin', 'email' => 'admin@test.com']);
    $admin->setPassword('123');
    $admin->isAdmin = 1;
    $admin->save(false);
    $this->adminId = $admin->id;

    // Створюємо автора коментаря
    $user = new User(['name' => 'Commenter', 'email' => 'user@test.com']);
    $user->setPassword('123');
    $user->isAdmin = 0;
    $user->save(false);
    $this->regularUserId = $user->id;

    // Створюємо топік та статтю
    $topic = new Topic(['name' => 'News']);
    $topic->save(false);

    $article = new Article([
      'title' => 'News Article',
      'topic_id' => $topic->id,
      'user_id' => $admin->id,
      'date' => date('Y-m-d')
    ]);
    $article->save(false);

    // Створюємо коментар для модерації
    $comment = new Comment([
      'text' => 'Bad Comment Content',
      'user_id' => $user->id,
      'article_id' => $article->id,
      'date' => date('Y-m-d H:i:s')
    ]);
    $comment->save(false);
    $this->commentId = $comment->id;
  }

  // ТЕСТ 1: Перевірка безпеки
  public function checkAccessControl(FunctionalTester $I)
  {
    // Гість
    $I->amOnPage('/index-test.php?r=admin/comment/index');
    $I->see('Login');

    // Звичайний юзер
    $I->amLoggedInAs($this->regularUserId);
    $I->amOnPage('/index-test.php?r=admin/comment/index');
    $I->seeResponseCodeIs(403);
  }

  // ТЕСТ 2: Список коментарів (Index)
  public function checkIndexPage(FunctionalTester $I)
  {
    $I->amLoggedInAs($this->adminId);
    $I->amOnPage('/index-test.php?r=admin/comment/index');

    $I->see('Comments', 'h1'); // Перевірте точний заголовок у вашому view
    $I->see('Bad Comment Content');
    $I->see('Commenter');
    $I->see('News Article');
  }

  // ТЕСТ 3: Модерація (Update)
  public function moderateComment(FunctionalTester $I)
  {
    $I->amLoggedInAs($this->adminId);
    $I->amOnPage('/index-test.php?r=admin/comment/update&id=' . $this->commentId);

    $I->see('Update Comment', 'h1');

    $I->fillField(['name' => 'Comment[text]'], 'Moderated Content: Is Good Now');
    $I->click('button[type=submit]');

    // ВИДАЛЯЄМО перевірку URL, бо вона конфліктує з кодуванням Windows
    // ЗАМІСТЬ НЕЇ перевіряємо, що ми знову бачимо головний заголовок списку
    $I->see('Comments', 'h1');

    // Перевіряємо, що текст дійсно оновився (це доводить успіх операції)
    $I->see('Moderated Content: Is Good Now');
    $I->dontSee('Bad Comment Content');
  }

  // ТЕСТ 4: Видалення (Delete)
  public function deleteComment(FunctionalTester $I)
  {
    $I->amLoggedInAs($this->adminId);

    // Видалення через POST запит
    $I->sendAjaxPostRequest('/index-test.php?r=admin/comment/delete&id=' . $this->commentId);

    $I->amOnPage('/index-test.php?r=admin/comment/index');
    $I->dontSee('Bad Comment Content');

    // Перевіряємо відсутність у БД
    $I->dontSeeRecord(Comment::class, ['id' => $this->commentId]);
  }
}
