<?php

use app\models\User;
use app\models\Article;
use app\models\Topic;

/**
 * Functional tests for front-end Article features.
 */
class ArticleCest
{
  private $userId;
  private $topicId;

  /**
   * Prepare test data: topic and user.
   */
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

  /**
   * Test 1: Index page as guest
   */
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

  /**
   * Test 2: Access control for create page
   */
  public function checkCreateAccessControl(FunctionalTester $I)
  {
    $I->amOnPage('/index-test.php?r=article/create');
    $I->see('Login');
    $I->seeInCurrentUrl('auth');
    $I->seeInCurrentUrl('login');
  }

  /**
   * Test 3: Create article successfully as logged-in user
   */
  public function createArticleSuccessfully(FunctionalTester $I)
  {
    $I->amLoggedInAs($this->userId);
    $I->amOnPage('/index-test.php?r=article/create');

    $I->fillField(['name' => 'Article[title]'], 'My Functional Test Article');
    $I->selectOption(['name' => 'Article[topic_id]'], (string)$this->topicId);
    $I->fillField(['name' => 'Article[description]'], 'This is a description content');
    $I->fillField(['name' => 'Article[tag]'], 'test, codeception');

    $I->click('Create Article');

    // Verify redirect to profile/index (user's articles)
    $I->seeInCurrentUrl('profile');
    $I->seeInCurrentUrl('index');

    // Verify DB record
    $I->seeRecord(Article::class, [
      'title' => 'My Functional Test Article',
      'user_id' => $this->userId
    ]);

    // Verify article displayed
    $I->see('My Functional Test Article');
  }

  /**
   * Test 4: Search functionality
   */
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
