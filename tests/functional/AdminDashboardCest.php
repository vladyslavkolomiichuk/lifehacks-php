<?php

use app\models\User;
use app\models\Article;
use app\models\Comment;
use app\models\Topic;

/**
 * Functional tests for admin dashboard.
 */
class AdminDashboardCest
{
  private $adminId;
  private $regularUserId;

  /**
   * Prepare test data: admin, regular user, topic, article, comment.
   */
  public function _before(FunctionalTester $I)
  {
    Comment::deleteAll();
    Article::deleteAll();
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

    // Topic
    $topic = new Topic(['name' => 'Tech']);
    $topic->save(false);

    // Article
    $article = new Article([
      'title' => 'Post 1',
      'topic_id' => $topic->id,
      'user_id' => $admin->id,
      'viewed' => 100,
      'date' => date('Y-m-d')
    ]);
    $article->save(false);

    // Comment
    $comment = new Comment([
      'text' => 'Nice',
      'user_id' => $user->id,
      'article_id' => $article->id,
      'date' => date('Y-m-d H:i:s')
    ]);
    $comment->save(false);
  }

  /**
   * Test 1: Access control - guest and regular user cannot access admin dashboard.
   */
  public function checkAccessControl(FunctionalTester $I)
  {
    // Guest
    $I->amOnPage('/index-test.php?r=admin/default/index');
    $I->see('Login');

    // Regular user
    $I->amLoggedInAs($this->regularUserId);
    $I->amOnPage('/index-test.php?r=admin/default/index');
    $I->seeResponseCodeIs(403);
  }

  /**
   * Test 2: Dashboard statistics and UI elements.
   */
  public function checkDashboardStats(FunctionalTester $I)
  {
    $I->amLoggedInAs($this->adminId);
    $I->amOnPage('/index-test.php?r=admin/default/index');

    $I->see('Dashboard', 'h1');

    // Check stat numbers
    $I->see('2', '.stat-card-value'); // Users count
    $I->see('1', '.stat-card-value'); // Articles count
    $I->see('100', '.stat-card-value'); // Total views

    // Check stat titles
    $I->see('Users', '.stat-card-title');
    $I->see('Articles', '.stat-card-title');
    $I->see('Comments', '.stat-card-title');

    // Quick Actions buttons
    $I->see('Add Topic', 'a');
    $I->see('Manage Admins', 'a');
    $I->see('Manage Articles', 'a');

    // 'View Details' links in cards
    $I->see('View Details', 'a');
  }
}
