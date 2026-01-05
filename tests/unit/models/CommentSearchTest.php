<?php

namespace tests\unit\models;

use app\models\CommentSearch;
use app\models\Comment;
use app\models\Article;
use app\models\User;
use app\models\Topic;
use Yii;

class CommentSearchTest extends \Codeception\Test\Unit
{
  protected $tester;

  // Глибока очистка бази через SQL
  protected function _before()
  {
    $db = Yii::$app->db;
    $db->createCommand('SET FOREIGN_KEY_CHECKS = 0')->execute();

    Comment::deleteAll();
    Article::deleteAll();
    Topic::deleteAll();
    User::deleteAll();

    $db->createCommand('SET FOREIGN_KEY_CHECKS = 1')->execute();
  }

  public function testSearch()
  {
    // Створюємо ОДНОГО юзера для Alice
    $userAlice = new User(['name' => 'Alice Unique', 'email' => 'alice_unique@test.com']);
    $userAlice->password = '123456';
    $userAlice->save(false);

    $topic = new Topic(['name' => 'General']);
    $topic->save(false);

    $article = new Article(['title' => 'Post', 'user_id' => $userAlice->id, 'topic_id' => $topic->id, 'date' => date('Y-m-d')]);
    $article->save(false);

    // Створюємо 2 коментарі саме від Alice з унікальним префіксом
    $c1 = new Comment(['text' => 'SEARCH_ME_1', 'user_id' => $userAlice->id, 'article_id' => $article->id]);
    $c1->save(false);
    $c2 = new Comment(['text' => 'SEARCH_ME_2', 'user_id' => $userAlice->id, 'article_id' => $article->id]);
    $c2->save(false);

    $searchModel = new CommentSearch();

    // Пошук за точною ID користувача Alice
    $params = ['CommentSearch' => ['user_id' => $userAlice->id]];
    $dataProvider = $searchModel->search($params);
    $this->assertEquals(2, $dataProvider->getTotalCount(), 'Має знайти 2 коментарі за ID Alice');

    // Пошук за унікальною строкою
    $params = ['CommentSearch' => ['text' => 'SEARCH_ME']];
    $dataProvider = $searchModel->search($params);
    $this->assertEquals(2, $dataProvider->getTotalCount(), 'Має знайти 2 коментарі за словом SEARCH_ME');
  }
}
