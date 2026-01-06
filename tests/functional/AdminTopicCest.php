<?php

use app\models\User;
use app\models\Topic;

/**
 * Functional tests for admin Topic management.
 */
class AdminTopicCest
{
  private $adminId;
  private $regularUserId;
  private $topicId;

  /**
   * Prepare test data: admin, regular user, existing topic.
   */
  public function _before(FunctionalTester $I)
  {
    Topic::deleteAll();
    User::deleteAll();

    // Admin user
    $admin = new User(['name' => 'Admin', 'email' => 'admin@test.com']);
    $admin->setPassword('123');
    $admin->isAdmin = 1;
    $admin->save(false);
    $this->adminId = $admin->id;

    // Regular user
    $user = new User(['name' => 'User', 'email' => 'user@test.com']);
    $user->setPassword('123');
    $user->isAdmin = 0;
    $user->save(false);
    $this->regularUserId = $user->id;

    // Existing topic
    $topic = new Topic(['name' => 'Existing Topic']);
    $topic->save(false);
    $this->topicId = $topic->id;
  }

  /**
   * Test 1: Access control - guest and regular user cannot access topic admin pages.
   */
  public function checkAccessControl(FunctionalTester $I)
  {
    // Guest
    $I->amOnPage('/index-test.php?r=admin/topic/index');
    $I->see('Login');

    // Regular user
    $I->amLoggedInAs($this->regularUserId);
    $I->amOnPage('/index-test.php?r=admin/topic/index');
    $I->seeResponseCodeIs(403);
  }

  /**
   * Test 2: Index page displays existing topics and create link.
   */
  public function checkIndexPage(FunctionalTester $I)
  {
    $I->amLoggedInAs($this->adminId);
    $I->amOnPage('/index-test.php?r=admin/topic/index');

    $I->see('Topics', 'h1');
    $I->see('Existing Topic');
    $I->seeLink('Create Topic');
  }

  /**
   * Test 3: Create new topic.
   */
  public function createTopic(FunctionalTester $I)
  {
    $I->amLoggedInAs($this->adminId);
    $I->amOnPage('/index-test.php?r=admin/topic/create');

    $I->see('Create Topic', 'h1');
    $I->fillField(['name' => 'Topic[name]'], 'New PHP Category');
    $I->click('button[type=submit]');

    $I->see('Topics', 'h1');
    $I->see('New PHP Category');
    $I->seeRecord(Topic::class, ['name' => 'New PHP Category']);
  }

  /**
   * Test 4: Update existing topic.
   */
  public function updateTopic(FunctionalTester $I)
  {
    $I->amLoggedInAs($this->adminId);
    $I->amOnPage('/index-test.php?r=admin/topic/update&id=' . $this->topicId);

    $I->see('Update Topic', 'h1');
    $I->fillField(['name' => 'Topic[name]'], 'Updated Topic Name');
    $I->click('button[type=submit]');

    $I->see('Topics', 'h1');
    $I->see('Updated Topic Name');
    $I->dontSee('Existing Topic');
  }

  /**
   * Test 5: Delete topic.
   */
  public function deleteTopic(FunctionalTester $I)
  {
    $I->amLoggedInAs($this->adminId);
    $I->sendAjaxPostRequest('/index-test.php?r=admin/topic/delete&id=' . $this->topicId);

    $I->amOnPage('/index-test.php?r=admin/topic/index');
    $I->dontSee('Existing Topic');
    $I->dontSeeRecord(Topic::class, ['id' => $this->topicId]);
  }
}
