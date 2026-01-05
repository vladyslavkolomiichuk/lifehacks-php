<?php

use app\models\User;
use app\models\Article;
use app\models\Topic;

class ArticleCest
{
  private $userId;
  private $topicId;

  public function _before(FunctionalTester $I)
  {
    Article::deleteAll();
    Topic::deleteAll();
    User::deleteAll();

    $topic = new Topic(['name' => 'LifeHacks']);
    $topic->save(false);
    $this->topicId = $topic->id;

    $user = new User();
    $user->name = 'Author';
    $user->email = 'author@test.com';
    $user->setPassword('123456');
    $user->save(false);
    $this->userId = $user->id;
  }

  public function checkIndexAsGuest(FunctionalTester $I)
  {
    $article = new Article([
      'title' => 'Public Article',
      'topic_id' => $this->topicId,
      'user_id' => $this->userId,
      'date' => date('Y-m-d'),
      'description' => 'Some content here'
    ]);
    $article->save(false);

    $I->amOnPage('/index-test.php?r=article/index');
    $I->seeInTitle('LifeHacks');
    $I->see('Public Article');
    $I->dontSeeLink('Create New');
  }

  public function checkCreateAccessControl(FunctionalTester $I)
  {
    $I->amOnPage('/index-test.php?r=article/create');

    $I->see('Login');
    // ВИПРАВЛЕННЯ: використовуємо seeInCurrentUrl замість регулярних виразів
    // Це найбільш стабільний метод для Windows/XAMPP
    $I->seeInCurrentUrl('auth');
    $I->seeInCurrentUrl('login');
  }

  public function createArticleSuccessfully(FunctionalTester $I)
  {
    $I->amLoggedInAs($this->userId);
    $I->amOnPage('/index-test.php?r=article/create');

    $I->fillField(['name' => 'Article[title]'], 'My Functional Test Article');
    // Вибираємо категорію (topicId має бути створений у _before)
    $I->selectOption(['name' => 'Article[topic_id]'], (string)$this->topicId);
    $I->fillField(['name' => 'Article[description]'], 'This is a description content');
    $I->fillField(['name' => 'Article[tag]'], 'test, codeception');

    // Клікаємо саме на кнопку з текстом "Create Article"
    $I->click('Create Article');

    // 1. ПЕРЕВІРКА РЕДИРЕКТУ (згідно з вашим контролером)
    $I->seeInCurrentUrl('profile');
    $I->seeInCurrentUrl('index');

    // 2. ПЕРЕВІРКА БАЗИ ДАНИХ
    $I->seeRecord(\app\models\Article::class, [
      'title' => 'My Functional Test Article',
      'user_id' => $this->userId
    ]);

    // 3. ПЕРЕВІРКА ВІДОБРАЖЕННЯ В КАБІНЕТІ
    $I->see('My Functional Test Article');
  }

  public function searchArticle(FunctionalTester $I)
  {
    $article = new Article([
      'title' => 'UniqueSearchTerm',
      'topic_id' => $this->topicId,
      'user_id' => $this->userId,
      'description' => 'Special text for search test'
    ]);
    $article->save(false);

    $I->amOnPage('/index-test.php?r=article/search&q=UniqueSearchTerm');
    $I->see('Search results for: "UniqueSearchTerm"');
    $I->see('UniqueSearchTerm');
  }
}
