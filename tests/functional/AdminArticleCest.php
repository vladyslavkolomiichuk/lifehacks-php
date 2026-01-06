<?php

use app\models\User;
use app\models\Article;
use app\models\Topic;

/**
 * Functional tests for admin article management.
 */
class AdminArticleCest
{
  private $adminId;
  private $regularUserId;
  private $topicId;
  private $articleId;

  /**
   * Runs before each test: sets up admin, regular user, topic, and article.
   */
  public function _before(FunctionalTester $I)
  {
    Article::deleteAll();
    Topic::deleteAll();
    User::deleteAll();

    // Create admin user
    $admin = new User(['name' => 'Admin', 'email' => 'admin@test.com']);
    $admin->setPassword('123');
    $admin->isAdmin = 1;
    $admin->save(false);
    $this->adminId = $admin->id;

    // Create regular user
    $user = new User(['name' => 'Simple', 'email' => 'user@test.com']);
    $user->setPassword('123');
    $user->isAdmin = 0;
    $user->save(false);
    $this->regularUserId = $user->id;

    // Create topic
    $topic = new Topic(['name' => 'News']);
    $topic->save(false);
    $this->topicId = $topic->id;

    // Create initial article
    $article = new Article([
      'title' => 'Existing Article',
      'description' => 'Desc',
      'topic_id' => $topic->id,
      'user_id' => $admin->id,
      'date' => date('Y-m-d')
    ]);
    $article->save(false);
    $this->articleId = $article->id;
  }

  /**
   * Test 1: Access control checks.
   */
  public function checkAccessControl(FunctionalTester $I)
  {
    // Guest access
    $I->amOnPage('/index-test.php?r=admin/article/index');
    $I->see('Login');

    // Regular user access
    $I->amLoggedInAs($this->regularUserId);
    $I->amOnPage('/index-test.php?r=admin/article/index');
    $I->seeResponseCodeIs(403);
  }

  /**
   * Test 2: Admin sees the articles index page.
   */
  public function checkIndexPage(FunctionalTester $I)
  {
    $I->amLoggedInAs($this->adminId);
    $I->amOnPage('/index-test.php?r=admin/article/index');

    $I->see('Articles', 'h1');
    $I->see('Existing Article');
    $I->see('News');
  }

  /**
   * Test 3: Admin creates a new article.
   */
  public function createArticle(FunctionalTester $I)
  {
    $I->amLoggedInAs($this->adminId);
    $I->amOnPage('/index-test.php?r=admin/article/create');

    $I->see('Create Article', 'h1');

    $I->fillField(['name' => 'Article[title]'], 'New Admin Article');
    $I->fillField(['name' => 'Article[description]'], 'Content created by admin');

    $I->selectOption(['name' => 'Article[topic_id]'], (string)$this->topicId);
    $I->selectOption(['name' => 'Article[user_id]'], (string)$this->adminId);

    $I->click('button[type=submit]');

    $I->see('New Admin Article', 'h1');
    $I->seeRecord(Article::class, ['title' => 'New Admin Article']);
  }

  /**
   * Test 4: Admin updates an existing article.
   */
  public function updateArticle(FunctionalTester $I)
  {
    $I->amLoggedInAs($this->adminId);
    $I->amOnPage('/index-test.php?r=admin/article/update&id=' . $this->articleId);

    $I->fillField(['name' => 'Article[title]'], 'Updated Title By Admin');
    $I->click('button[type=submit]');

    $I->see('Updated Title By Admin', 'h1');
  }

  /**
   * Test 5: Admin deletes an article.
   */
  public function deleteArticle(FunctionalTester $I)
  {
    $I->amLoggedInAs($this->adminId);

    $I->sendAjaxPostRequest('/index-test.php?r=admin/article/delete&id=' . $this->articleId);

    $I->amOnPage('/index-test.php?r=admin/article/index');
    $I->dontSee('Existing Article');
    $I->dontSeeRecord(Article::class, ['id' => $this->articleId]);
  }
}
