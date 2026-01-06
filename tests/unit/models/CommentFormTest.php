<?php

namespace tests\unit\models;

use Yii;
use app\models\CommentForm;
use app\models\User;
use app\models\Article;
use app\models\Topic;
use app\models\Comment;

/**
 * Unit tests for CommentForm model.
 */
class CommentFormTest extends \Codeception\Test\Unit
{
  /**
   * @var \UnitTester
   */
  protected $tester;

  /**
   * Clean database before each test.
   */
  protected function _before()
  {
    Comment::deleteAll();
    Article::deleteAll();
    Topic::deleteAll();
    User::deleteAll();
  }

  /**
   * Test validation rules of CommentForm.
   */
  public function testValidation()
  {
    $form = new CommentForm();

    // Scenario 1: Empty comment
    $form->comment = null;
    $this->assertFalse($form->validate(['comment']), 'Comment is required');

    // Scenario 2: Too short comment (min 3)
    $form->comment = 'Hi';
    $this->assertFalse($form->validate(['comment']), 'Comment is too short');

    // Scenario 3: Too long comment (max 250)
    $form->comment = str_repeat('a', 251);
    $this->assertFalse($form->validate(['comment']), 'Comment is too long');

    // Scenario 4: Valid comment
    $form->comment = 'Good article!';
    $this->assertTrue($form->validate(['comment']), 'Valid comment should pass validation');

    // Scenario 5: parentId must be integer
    $form->parentId = 'abc';
    $this->assertFalse($form->validate(['parentId']), 'Parent ID must be an integer');
  }

  /**
   * Test saving a comment via saveComment().
   */
  public function testSaveComment()
  {
    // 1. Prepare related models
    $user = new User(['name' => 'Tester', 'email' => 't@t.com']);
    $user->setPassword('123');
    $user->save(false);

    $topic = new Topic(['name' => 'Tech']);
    $topic->save(false);

    $article = new Article([
      'title' => 'News',
      'user_id' => $user->id,
      'topic_id' => $topic->id,
      'date' => date('Y-m-d')
    ]);
    $article->save(false);

    // 2. Log in the user
    Yii::$app->user->login($user);

    // 3. Fill CommentForm
    $form = new CommentForm();
    $form->comment = 'This is a test comment';

    // 4. Save the comment
    $result = $form->saveComment($article->id);

    // 5. Assertions
    $this->assertTrue($result, 'saveComment should return true');

    $savedComment = Comment::findOne(['text' => 'This is a test comment']);
    $this->assertNotNull($savedComment, 'Comment should exist in DB');

    $this->assertEquals($user->id, $savedComment->user_id, 'User ID should match logged-in user');
    $this->assertEquals($article->id, $savedComment->article_id, 'Article ID should match');
    $this->assertEquals(date('Y-m-d'), $savedComment->date, 'Date should be today');
  }
}
