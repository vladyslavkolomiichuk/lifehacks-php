<?php

namespace tests\unit\models;

use app\models\Comment;
use app\models\Article;
use app\models\User;
use app\models\Topic;

/**
 * Unit tests for Comment model.
 */
class CommentTest extends \Codeception\Test\Unit
{
  /**
   * @var \UnitTester
   */
  protected $tester;

  /**
   * Clean DB before each test.
   */
  protected function _before()
  {
    Comment::deleteAll();
    Article::deleteAll();
    Topic::deleteAll();
    User::deleteAll();
  }

  /**
   * Test validation rules.
   */
  public function testValidation()
  {
    $comment = new Comment();

    // Text too long
    $comment->text = str_repeat('a', 256);
    $this->assertFalse($comment->validate(['text']), 'Text should be max 255 chars');

    // Valid text
    $comment->text = 'Valid comment';
    $this->assertTrue($comment->validate(['text']));

    // Non-integer user_id
    $comment->user_id = 'abc';
    $this->assertFalse($comment->validate(['user_id']), 'User ID must be integer');
  }

  /**
   * Test saving and basic relations.
   */
  public function testSavingAndRelations()
  {
    $user = new User(['name' => 'Commenter', 'email' => 'c@test.com', 'password' => '123']);
    $user->save(false);

    $topic = new Topic(['name' => 'General']);
    $topic->save(false);

    $article = new Article([
      'title' => 'Article for comments',
      'user_id' => $user->id,
      'topic_id' => $topic->id
    ]);
    $article->save(false);

    $comment = new Comment([
      'text' => 'Great article!',
      'user_id' => $user->id,
      'article_id' => $article->id,
      'date' => date('Y-m-d')
    ]);

    $this->assertTrue($comment->save(), 'Comment should be saved');
    $this->assertNotNull($comment->user);
    $this->assertEquals($user->name, $comment->user->name);
    $this->assertNotNull($comment->article);
    $this->assertEquals($article->title, $comment->article->title);
  }

  /**
   * Test parent-child comment hierarchy.
   */
  public function testParentChildHierarchy()
  {
    $user = new User(['name' => 'User', 'email' => 'u@test.com', 'password' => '123']);
    $user->save(false);

    $topic = new Topic(['name' => 'Topic']);
    $topic->save(false);

    $article = new Article(['title' => 'Article', 'user_id' => $user->id, 'topic_id' => $topic->id]);
    $article->save(false);

    $parent = new Comment([
      'text' => 'Parent comment',
      'user_id' => $user->id,
      'article_id' => $article->id
    ]);
    $parent->save();

    $child = new Comment([
      'text' => 'Child reply',
      'user_id' => $user->id,
      'article_id' => $article->id,
      'parent_id' => $parent->id
    ]);
    $this->assertTrue($child->save(), 'Child comment should save with valid parent_id');

    $parent->refresh();
    $this->assertNotEmpty($parent->children);
    $this->assertEquals(1, count($parent->children));
    $this->assertEquals('Child reply', $parent->children[0]->text);
    $this->assertNotNull($child->parent);
    $this->assertEquals($parent->id, $child->parent->id);
  }

  /**
   * Test invalid parent_id.
   */
  public function testInvalidParent()
  {
    $user = new User(['name' => 'User', 'email' => 'u2@test.com', 'password' => '123']);
    $user->save(false);

    $topic = new Topic(['name' => 'Topic2']);
    $topic->save(false);

    $article = new Article(['title' => 'Article2', 'user_id' => $user->id, 'topic_id' => $topic->id]);
    $article->save(false);

    $comment = new Comment([
      'text' => 'Orphan comment',
      'user_id' => $user->id,
      'article_id' => $article->id,
      'parent_id' => 99999 // Non-existent parent
    ]);

    $this->assertFalse($comment->validate(['parent_id']), 'Should not validate if parent_id does not exist');
  }
}
