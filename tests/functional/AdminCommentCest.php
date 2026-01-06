<?php

use app\models\User;
use app\models\Article;
use app\models\Topic;
use app\models\Comment;

/**
 * Functional tests for admin comment management.
 */
class AdminCommentCest
{
  private $adminId;
  private $regularUserId;
  private $commentId;

  /**
   * Sets up admin, regular user, topic, article, and comment before each test.
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

    // Regular user (comment author)
    $user = new User(['name' => 'Commenter', 'email' => 'user@test.com']);
    $user->setPassword('123');
    $user->isAdmin = 0;
    $user->save(false);
    $this->regularUserId = $user->id;

    // Topic
    $topic = new Topic(['name' => 'News']);
    $topic->save(false);

    // Article
    $article = new Article([
      'title' => 'News Article',
      'topic_id' => $topic->id,
      'user_id' => $admin->id,
      'date' => date('Y-m-d')
    ]);
    $article->save(false);

    // Comment
    $comment = new Comment([
      'text' => 'Bad Comment Content',
      'user_id' => $user->id,
      'article_id' => $article->id,
      'date' => date('Y-m-d H:i:s')
    ]);
    $comment->save(false);
    $this->commentId = $comment->id;
  }

  /**
   * Test 1: Access control - guest and regular user cannot access admin.
   */
  public function checkAccessControl(FunctionalTester $I)
  {
    // Guest
    $I->amOnPage('/index-test.php?r=admin/comment/index');
    $I->see('Login');

    // Regular user
    $I->amLoggedInAs($this->regularUserId);
    $I->amOnPage('/index-test.php?r=admin/comment/index');
    $I->seeResponseCodeIs(403);
  }

  /**
   * Test 2: Admin sees comments index page.
   */
  public function checkIndexPage(FunctionalTester $I)
  {
    $I->amLoggedInAs($this->adminId);
    $I->amOnPage('/index-test.php?r=admin/comment/index');

    $I->see('Comments', 'h1');
    $I->see('Bad Comment Content');
    $I->see('Commenter');
    $I->see('News Article');
  }

  /**
   * Test 3: Admin moderates a comment.
   */
  public function moderateComment(FunctionalTester $I)
  {
    $I->amLoggedInAs($this->adminId);
    $I->amOnPage('/index-test.php?r=admin/comment/update&id=' . $this->commentId);

    $I->see('Update Comment', 'h1');

    $I->fillField(['name' => 'Comment[text]'], 'Moderated Content: Is Good Now');
    $I->click('button[type=submit]');

    // Verify redirect back to index and content updated
    $I->see('Comments', 'h1');
    $I->see('Moderated Content: Is Good Now');
    $I->dontSee('Bad Comment Content');
  }

  /**
   * Test 4: Admin deletes a comment.
   */
  public function deleteComment(FunctionalTester $I)
  {
    $I->amLoggedInAs($this->adminId);

    $I->sendAjaxPostRequest('/index-test.php?r=admin/comment/delete&id=' . $this->commentId);

    $I->amOnPage('/index-test.php?r=admin/comment/index');
    $I->dontSee('Bad Comment Content');
    $I->dontSeeRecord(Comment::class, ['id' => $this->commentId]);
  }
}
