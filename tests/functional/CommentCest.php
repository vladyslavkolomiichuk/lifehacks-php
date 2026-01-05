<?php

use app\models\User;
use app\models\Article;
use app\models\Topic;
use app\models\Comment;

class CommentCest
{
  private $userId;
  private $articleId;

  public function _before(FunctionalTester $I)
  {
    // Очистка
    Comment::deleteAll();
    Article::deleteAll();
    Topic::deleteAll();
    User::deleteAll();

    // Фікстури
    $user = new User(['name' => 'Commenter', 'email' => 'c@c.com', 'password' => '123']);
    $user->save(false);
    $this->userId = $user->id;

    $topic = new Topic(['name' => 'General']);
    $topic->save(false);

    $article = new Article([
      'title' => 'Article for Comments',
      'topic_id' => $topic->id,
      'user_id' => $user->id,
      'date' => date('Y-m-d')
    ]);
    $article->save(false);
    $this->articleId = $article->id;
  }

  // ТЕСТ 1: Гість не бачить форму, але бачить прохання увійти
  public function guestCannotComment(FunctionalTester $I)
  {
    $I->amOnPage(['article/view', 'id' => $this->articleId]);

    $I->see('Article for Comments', 'h1');

    // Перевіряємо наявність повідомлення для гостей
    $I->see('Please');
    $I->seeLink('Login', '/auth/login'); // або посилання, яке генерує Url::to

    // Перевіряємо відсутність форми
    $I->dontSeeElement('#comment-textarea');
    $I->dontSee('Post Comment');
  }

  // ТЕСТ 2: Авторизований користувач може коментувати
  public function userCanPostComment(FunctionalTester $I)
  {
    $I->amLoggedInAs($this->userId);
    $I->amOnPage(['article/view', 'id' => $this->articleId]);

    // Тепер форма має бути
    $I->seeElement('#comment-textarea');

    // Відправляємо форму
    // Зверніть увагу: ми використовуємо імена полів з CommentForm
    $I->submitForm('.contact-form', [
      'CommentForm[comment]' => 'This is a functional test comment!',
      'CommentForm[parentId]' => '', // Пусте для кореневого коментаря
    ]);

    // Контролер перекидає назад на view
    $I->seeCurrentUrlMatches("~article/view~");
    $I->see('Comment added'); // Flash message

    // Перевіряємо, чи з'явився текст на сторінці
    $I->see('This is a functional test comment!');
    $I->see('Commenter'); // Ім'я автора

    // Перевіряємо лічильник коментарів (у вас id="comment-count")
    $I->see('1', '#comment-count');
  }
}
