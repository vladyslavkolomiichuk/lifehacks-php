<?php

use app\models\User;
use app\models\Article;
use app\models\Topic;
use app\models\Vote;

/**
 * Functional tests for admin Vote management.
 */
class AdminVoteCest
{
  private $adminId;
  private $regularUserId;
  private $voteId;
  private $articleId;

  /**
   * Prepare test data: admin, user, article, and vote.
   */
  public function _before(FunctionalTester $I)
  {
    // Clean DB
    Vote::deleteAll();
    Article::deleteAll();
    Topic::deleteAll();
    User::deleteAll();

    // Admin
    $admin = new User(['name' => 'Admin', 'email' => 'admin@test.com']);
    $admin->setPassword('123');
    $admin->isAdmin = 1;
    $admin->save(false);
    $this->adminId = $admin->id;

    // Regular user
    $user = new User(['name' => 'Voter', 'email' => 'user@test.com']);
    $user->setPassword('123');
    $user->isAdmin = 0;
    $user->save(false);
    $this->regularUserId = $user->id;

    // Topic & Article
    $topic = new Topic(['name' => 'News']);
    $topic->save(false);

    $article = new Article([
      'title' => 'Liked Article',
      'topic_id' => $topic->id,
      'user_id' => $admin->id,
      'upvotes' => 1,
      'date' => date('Y-m-d')
    ]);
    $article->save(false);
    $this->articleId = $article->id;

    // Vote
    $vote = new Vote([
      'user_id' => $user->id,
      'article_id' => $article->id
    ]);
    $vote->save(false);
    $this->voteId = $vote->id;
  }

  /**
   * Test 1: Access control
   */
  public function checkAccessControl(FunctionalTester $I)
  {
    // Guest
    $I->amOnPage('/index-test.php?r=admin/vote/index');
    $I->see('Login');

    // Regular user
    $I->amLoggedInAs($this->regularUserId);
    $I->amOnPage('/index-test.php?r=admin/vote/index');
    $I->seeResponseCodeIs(403);
  }

  /**
   * Test 2: Index page lists votes
   */
  public function checkIndexPage(FunctionalTester $I)
  {
    $I->amLoggedInAs($this->adminId);
    $I->amOnPage('/index-test.php?r=admin/vote/index');

    $I->see('Votes', 'h1');
    $I->see('Voter');
    $I->see('Liked Article');
  }

  /**
   * Test 3: Delete vote and verify article counter
   */
  public function deleteVote(FunctionalTester $I)
  {
    $I->amLoggedInAs($this->adminId);

    $I->sendAjaxPostRequest('/index-test.php?r=admin/vote/delete&id=' . $this->voteId);

    $I->amOnPage('/index-test.php?r=admin/vote/index');
    $I->dontSee('Liked Article');
    $I->dontSeeRecord(Vote::class, ['id' => $this->voteId]);

    // Verify article upvotes decremented
    $I->seeRecord(Article::class, [
      'id' => $this->articleId,
      'upvotes' => 0
    ]);
  }
}
