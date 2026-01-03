<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * LoginForm is the model behind the login form.
 */
class LoginForm extends Model
{
  // 1. ВИПРАВЛЕНО: Змінили $username на $email
  public $email;
  public $password;
  public $rememberMe = true;

  // 2. ВИПРАВЛЕНО: Ініціалізуємо як false (прапор, що ще не завантажено),
  // але phpdoc допомагає IDE зрозуміти типи.
  private $_user = false;

  public function rules()
  {
    return [
      // 3. ВИПРАВЛЕНО: Валідуємо email замість username
      [['email', 'password'], 'required'],
      ['email', 'email'], // Додали перевірку на формат email
      ['rememberMe', 'boolean'],
      ['password', 'validatePassword'],
    ];
  }

  /**
   * Validates the password.
   */
  public function validatePassword($attribute, $params)
  {
    if (!$this->hasErrors()) {
      $user = $this->getUser();

      // Перевірка: Якщо користувача немає АБО пароль невірний
      if (!$user || !$user->validatePassword($this->password)) {
        // 4. ВИПРАВЛЕНО: Текст помилки
        $this->addError($attribute, 'Incorrect email or password.');
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

      // Переконуємося, що це саме об'єкт User, а не щось інше
      if ($user) {
        return Yii::$app->user->login($user, $this->rememberMe ? 3600 * 24 * 30 : 0);
      }
    }
    return false;
  }

  /**
   * Отримання користувача
   * @return User|null
   */
  public function getUser()
  {
    if ($this->_user === false) {
      // 5. ВИПРАВЛЕНО: Використовуємо $this->email, який ми оголосили зверху
      $this->_user = User::findOne(['email' => $this->email]);
    }

    return $this->_user;
  }
}
