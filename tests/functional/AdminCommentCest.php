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
    // 1. Очищення бази
    Comment::deleteAll();
    Article::deleteAll();
    Topic::deleteAll();
    User::deleteAll();

    // 2. Створюємо Адміна
    $admin = new User(['name' => 'Admin', 'email' => 'admin@test.com', 'password' => '123']);
    $admin->isAdmin = 1;
    $admin->save(false);
    $this->adminId = $admin->id;

    // 3. Створюємо звичайного юзера (автора коментаря)
    $user = new User(['name' => 'Commenter', 'email' => 'user@test.com', 'password' => '123']);
    $user->isAdmin = 0;
    $user->save(false);
    $this->regularUserId = $user->id;

    // 4. Створюємо статтю (бо коментар прив'язаний до статті)
    $topic = new Topic(['name' => 'News']);
    $topic->save(false);

    $article = new Article([
      'title' => 'News Article',
      'topic_id' => $topic->id,
      'user_id' => $admin->id,
      'date' => date('Y-m-d')
    ]);
    $article->save(false);

    // 5. Створюємо коментар, який будемо модерувати
    $comment = new Comment([
      'text' => 'Bad Comment Content', // Текст, який ми змінимо
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
    $I->amOnPage(['admin/comment/index']);
    $I->see('Login');

    // Звичайний юзер
    $I->amLoggedInAs($this->regularUserId);
    $I->amOnPage(['admin/comment/index']);
    $I->seeResponseCodeIs(403);
  }

  // ТЕСТ 2: Список коментарів (Index)
  public function checkIndexPage(FunctionalTester $I)
  {
    $I->amLoggedInAs($this->adminId);
    $I->amOnPage(['admin/comment/index']);

    $I->see('Comments Manager', 'h1');
    // GridView скорочує текст до 50 символів, але наш короткий, тому має бути видно весь
    $I->see('Bad Comment Content');
    $I->see('Commenter'); // Автор
    $I->see('News Article'); // Стаття
  }

  // ТЕСТ 3: Модерація (Update)
  public function moderateComment(FunctionalTester $I)
  {
    $I->amLoggedInAs($this->adminId);

    // Заходимо на сторінку редагування
    $I->amOnPage(['admin/comment/update', 'id' => $this->commentId]);

    $I->see('Update Comment #' . $this->commentId, 'h1');
    $I->see('Bad Comment Content'); // Бачимо старий текст у полі

    // Змінюємо текст (модеруємо)
    // Використовуємо селектор .dark-form, який є у вашому comment/_form.php
    $I->submitForm('.dark-form', [
      'Comment[text]' => 'Moderated Content: Is Good Now',
    ]);

    // Має перекинути на index
    $I->seeCurrentUrlMatches('~admin/comment/index~');

    // Перевіряємо, чи змінився текст у списку
    $I->see('Moderated Content: Is Good Now');
    $I->dontSee('Bad Comment Content');
  }

  // ТЕСТ 4: Видалення (Delete)
  public function deleteComment(FunctionalTester $I)
  {
    $I->amLoggedInAs($this->adminId);

    // Відправляємо POST запит на видалення
    $I->sendPost(['admin/comment/delete', 'id' => $this->commentId]);

    // Перевіряємо, що коментар зник
    $I->amOnPage(['admin/comment/index']);
    $I->dontSee('Bad Comment Content');

    // Перевіряємо базу даних
    $I->dontSeeRecord(Comment::class, ['id' => $this->commentId]);
  }
}
