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
    // Повне очищення
    Comment::deleteAll();
    Article::deleteAll();
    Topic::deleteAll();
    User::deleteAll();

    // Створюємо автора
    $user = new User(['name' => 'Commenter', 'email' => 'c@c.com']);
    $user->setPassword('123');
    $user->save(false);
    $this->userId = $user->id;

    $topic = new Topic(['name' => 'General']);
    $topic->save(false);

    // Створюємо статтю, яку будемо коментувати
    $article = new Article([
      'title' => 'Article for Comments',
      'topic_id' => $topic->id,
      'user_id' => $user->id,
      'date' => date('Y-m-d')
    ]);
    $article->save(false);
    $this->articleId = $article->id;
  }

  // ТЕСТ 1: Гість не бачить форму
  public function guestCannotComment(FunctionalTester $I)
  {
    $I->amOnPage('/index-test.php?r=article/view&id=' . $this->articleId);

    $I->see('Article for Comments', 'h1');

    // Перевіряємо текст
    $I->see('Please');

    // Перевіряємо посилання тільки за текстом (це надійніше для Yii2 URL-ів)
    $I->seeLink('Login');

    // Перевіряємо, що форми немає
    $I->dontSeeElement('#comment-textarea');
  }

  // ТЕСТ 2: Авторизований користувач може коментувати
  public function userCanPostComment(FunctionalTester $I)
  {
    $I->amLoggedInAs($this->userId);
    $I->amOnPage('/index-test.php?r=article/view&id=' . $this->articleId);

    $I->seeElement('#comment-textarea');

    $I->fillField(['name' => 'CommentForm[comment]'], 'This is a functional test comment!');
    $I->click('Post Comment');

    // 1. Перевірка редиректу (враховуємо %2F)
    $I->seeInCurrentUrl('r=article');
    $I->seeInCurrentUrl('view');

    // 2. Перевірка бази даних (найважливіша частина!)
    $I->seeRecord(\app\models\Comment::class, [
      'text' => 'This is a functional test comment!',
      'article_id' => $this->articleId,
      'user_id' => $this->userId
    ]);

    // 3. Перевірка відображення на сторінці
    $I->see('This is a functional test comment!');
    $I->see('Commenter');
  }
}
