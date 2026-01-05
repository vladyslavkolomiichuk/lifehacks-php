<?php

namespace tests\unit\models;

use app\models\UserSearch;
use app\models\User;
use Yii;

class UserSearchTest extends \Codeception\Test\Unit
{
  /**
   * @var \UnitTester
   */
  protected $tester;

  protected function _before()
  {
    $db = \Yii::$app->db;
    // Вимикаємо перевірку ключів
    $db->createCommand('SET FOREIGN_KEY_CHECKS = 0')->execute();

    // Очищаємо основні таблиці
    $db->createCommand()->truncateTable('vote')->execute();
    $db->createCommand()->truncateTable('comment')->execute();
    $db->createCommand()->truncateTable('article')->execute();
    $db->createCommand()->truncateTable('user')->execute();
    $db->createCommand()->truncateTable('topic')->execute();

    // Вмикаємо назад
    $db->createCommand('SET FOREIGN_KEY_CHECKS = 1')->execute();
  }

  public function testSearch()
  {
    $user = new User(['name' => 'RegularUser', 'email' => 'reg@test.com', 'isAdmin' => 0]);
    $user->password = '123456';
    $user->save(false);

    $searchModel = new UserSearch();
    // Використовуйте повне ім'я класу в масиві, або перевірте як працює load()
    $params = ['UserSearch' => ['name' => 'RegularUser']];
    $dataProvider = $searchModel->search($params);

    $this->assertEquals(1, $dataProvider->getTotalCount(), 'Має знайти користувача RegularUser');
  }
}
