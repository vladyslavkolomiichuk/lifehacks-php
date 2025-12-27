<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * LoginForm is the model behind the login form.
 */
class LoginForm extends Model
{
  public $username;
  public $password;
  public $rememberMe = true;

  private $_user = false;

  public function rules()
  {
    return [
      [['username', 'password'], 'required'],
      ['rememberMe', 'boolean'],
      ['password', 'validatePassword'],
    ];
  }

  public function validatePassword($attribute, $params)
  {
    if (!$this->hasErrors()) {
      $user = $this->getUser();

      // Перевіряємо, чи це об'єкт класу User
      if (!$user || !($user instanceof User) || !$user->validatePassword($this->password)) {
        $this->addError($attribute, 'Невірний логін або пароль.');
      }
    }
  }

  /**
   * Вхід користувача.
   */
  public function login()
  {
    if ($this->validate()) {
      $user = $this->getUser();

      // ВАЖЛИВЕ ВИПРАВЛЕННЯ:
      // Використовуємо instanceof, щоб гарантувати, що передаємо об'єкт, а не true/false
      if ($user instanceof User) {
        return Yii::$app->user->login($user, $this->rememberMe ? 3600 * 24 * 30 : 0);
      }
    }
    return false;
  }

  /**
   * Отримання користувача
   */
  public function getUser()
  {
    if ($this->_user === false) {
      // Шукаємо користувача в БД
      $this->_user = User::findOne(['login' => $this->username]);
    }

    // Якщо користувача не знайдено, findOne поверне null.
    // Це безпечно для перевірки, але повернення має бути User|null|false
    return $this->_user;
  }
}
