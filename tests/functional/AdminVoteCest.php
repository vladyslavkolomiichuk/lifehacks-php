<?php

use app\models\User;
use app\models\Article;
use app\models\Topic;
use app\models\Vote;

class AdminVoteCest
{
  private $adminId;
  private $regularUserId;
  private $voteId;
  private $articleId;

  public function _before(FunctionalTester $I)
  {
    // 1. Очищення
    Vote::deleteAll();
    Article::deleteAll();
    Topic::deleteAll();
    User::deleteAll();

    // 2. Створюємо Адміна
    $admin = new User(['name' => 'Admin', 'email' => 'admin@test.com', 'password' => '123']);
    $admin->isAdmin = 1;
    $admin->save(false);
    $this->adminId = $admin->id;

    // 3. Створюємо Звичайного юзера (який поставив лайк)
    $user = new User(['name' => 'Voter', 'email' => 'user@test.com', 'password' => '123']);
    $user->isAdmin = 0;
    $user->save(false);
    $this->regularUserId = $user->id;

    // 4. Створюємо статтю (з 1 лайком)
    $topic = new Topic(['name' => 'News']);
    $topic->save(false);

    $article = new Article([
      'title' => 'Liked Article',
      'topic_id' => $topic->id,
      'user_id' => $admin->id,
      'upvotes' => 1, // Початковий стан лічильника
      'date' => date('Y-m-d')
    ]);
    $article->save(false);
    $this->articleId = $article->id;

    // 5. Створюємо Лайк
    $vote = new Vote([
      'user_id' => $user->id,
      'article_id' => $article->id
    ]);
    $vote->save(false);
    $this->voteId = $vote->id;
  }

  // ТЕСТ 1: Безпека
  public function checkAccessControl(FunctionalTester $I)
  {
    $I->amOnPage(['admin/vote/index']);
    $I->see('Login');

    $I->amLoggedInAs($this->regularUserId);
    $I->amOnPage(['admin/vote/index']);
    $I->seeResponseCodeIs(403);
  }

  // ТЕСТ 2: Список лайків
  public function checkIndexPage(FunctionalTester $I)
  {
    $I->amLoggedInAs($this->adminId);
    $I->amOnPage(['admin/vote/index']);

    $I->see('Votes Manager', 'h1');
    $I->see('Voter'); // Ім'я користувача
    $I->see('Liked Article'); // Назва статті
  }

  // ТЕСТ 3: Видалення лайка (найважливіший тест)
  public function deleteVote(FunctionalTester $I)
  {
    $I->amLoggedInAs($this->adminId);

    // Видаляємо лайк
    $I->sendPost(['admin/vote/delete', 'id' => $this->voteId]);

    // Перевіряємо редірект
    $I->amOnPage(['admin/vote/index']);
    $I->dontSee('Liked Article'); // Лайк зник зі списку

    // 1. Перевіряємо, що лайк зник з таблиці Vote
    $I->dontSeeRecord(Vote::class, ['id' => $this->voteId]);

    // 2. ПЕРЕВІРЯЄМО ЛІЧИЛЬНИК У СТАТТІ
    // Було 1, має стати 0
    $I->seeRecord(Article::class, [
      'id' => $this->articleId,
      'upvotes' => 0
    ]);
  }
}
