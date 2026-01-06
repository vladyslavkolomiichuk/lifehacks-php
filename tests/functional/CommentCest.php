<?php

use app\models\User;
use app\models\Article;
use app\models\Topic;
use app\models\Comment;

/**
 * Functional tests for front-end Comment functionality.
 */
class CommentCest
{
  private $userId;
  private $articleId;

  /**
   * Prepare test data: user, topic, article.
   */
  public function _before(FunctionalTester $I)
  {
    Comment::deleteAll();
    Article::deleteAll();
    Topic::deleteAll();
    User::deleteAll();

    // 1. Create user
    $user = new User(['name' => 'Commenter', 'email' => 'c@c.com']);
    $user->setPassword('123');
    $user->save(false);
    $this->userId = $user->id;

    // 2. Create topic
    $topic = new Topic(['name' => 'General']);
    $topic->save(false);

    // 3. Create article
    $article = new Article([
      'title' => 'Article for Comments',
      'topic_id' => $topic->id,
      'user_id' => $user->id,
      'date' => date('Y-m-d')
    ]);
    $article->save(false);
    $this->articleId = $article->id;
  }

  /**
   * Test 1: Guest cannot see comment form
   */
  public function guestCannotComment(FunctionalTester $I)
  {
    $I->amOnPage('/index-test.php?r=article/view&id=' . $this->articleId);

    $I->see('Article for Comments', 'h1');
    $I->see('Please');
    $I->seeLink('Login');

    // Ensure textarea/form for comments is not present
    $I->dontSeeElement('#comment-textarea');
  }

  /**
   * Test 2: Logged-in user can post a comment
   */
  public function userCanPostComment(FunctionalTester $I)
  {
    $I->amLoggedInAs($this->userId);
    $I->amOnPage('/index-test.php?r=article/view&id=' . $this->articleId);

    $I->seeElement('#comment-textarea');

    $I->fillField(['name' => 'CommentForm[comment]'], 'This is a functional test comment!');
    $I->click('Post Comment');

    // Verify redirect back to article view
    $I->seeInCurrentUrl('r=article');
    $I->seeInCurrentUrl('view');

    // Verify comment is stored in DB
    $I->seeRecord(Comment::class, [
      'text' => 'This is a functional test comment!',
      'article_id' => $this->articleId,
      'user_id' => $this->userId
    ]);

    // Verify comment is displayed on page
    $I->see('This is a functional test comment!');
    $I->see('Commenter');
  }
}
