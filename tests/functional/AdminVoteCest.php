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
    // 1. Повне очищення для чистоти експерименту
    Vote::deleteAll();
    Article::deleteAll();
    Topic::deleteAll();
    User::deleteAll();

    // 2. Створюємо Адміна
    $admin = new User(['name' => 'Admin', 'email' => 'admin@test.com']);
    $admin->setPassword('123');
    $admin->isAdmin = 1;
    $admin->save(false);
    $this->adminId = $admin->id;

    // 3. Створюємо Користувача
    $user = new User(['name' => 'Voter', 'email' => 'user@test.com']);
    $user->setPassword('123');
    $user->isAdmin = 0;
    $user->save(false);
    $this->regularUserId = $user->id;

    // 4. Створюємо статтю
    $topic = new Topic(['name' => 'News']);
    $topic->save(false);

    $article = new Article([
      'title' => 'Liked Article',
      'topic_id' => $topic->id,
      'user_id' => $admin->id,
      'upvotes' => 1, // Початковий стан
      'date' => date('Y-m-d')
    ]);
    $article->save(false);
    $this->articleId = $article->id;

    // 5. Створюємо Лайк (зв'язок)
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
    // Гість
    $I->amOnPage('/index-test.php?r=admin/vote/index');
    $I->see('Login');

    // Юзер без прав
    $I->amLoggedInAs($this->regularUserId);
    $I->amOnPage('/index-test.php?r=admin/vote/index');
    $I->seeResponseCodeIs(403);
  }

  // ТЕСТ 2: Список лайків
  public function checkIndexPage(FunctionalTester $I)
  {
    $I->amLoggedInAs($this->adminId);
    $I->amOnPage('/index-test.php?r=admin/vote/index');

    $I->see('Votes', 'h1'); // Перевірте заголовок у вашому view (Votes чи Votes Manager)
    $I->see('Voter');
    $I->see('Liked Article');
  }

  // ТЕСТ 3: Видалення лайка та перевірка лічильника
  public function deleteVote(FunctionalTester $I)
  {
    $I->amLoggedInAs($this->adminId);

    // Видаляємо лайк через AJAX POST
    $I->sendAjaxPostRequest('/index-test.php?r=admin/vote/delete&id=' . $this->voteId);

    // Повертаємось на сторінку списку
    $I->amOnPage('/index-test.php?r=admin/vote/index');
    $I->dontSee('Liked Article');

    // 1. Перевіряємо фізичну відсутність запису лайка
    $I->dontSeeRecord(Vote::class, ['id' => $this->voteId]);

    // 2. ПЕРЕВІРЯЄМО ЛІЧИЛЬНИК У СТАТТІ (має стати 0)
    // Це спрацює, якщо у вас в Vote::afterDelete() або контролері прописано decrement
    $I->seeRecord(Article::class, [
      'id' => $this->articleId,
      'upvotes' => 0
    ]);
  }
}
