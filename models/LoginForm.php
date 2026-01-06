<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * Form model for user authentication.
 */
class LoginForm extends Model
{
  public $email;
  public $password;
  public $rememberMe = true;

  // Cached user instance
  private $_user = false;

  /**
   * Validation rules.
   */
  public function rules()
  {
    return [
      [['email', 'password'], 'required'],
      ['email', 'email'],
      ['rememberMe', 'boolean'],
      ['password', 'validatePassword'],
    ];
  }

  /**
   * Validates the provided password.
   */
  public function validatePassword($attribute, $params)
  {
    if (!$this->hasErrors()) {
      $user = $this->getUser();

      if (!$user || !$user->validatePassword($this->password)) {
        $this->addError($attribute, 'Incorrect email or password.');
      }
    }
  }

  /**
   * Logs in the user.
   */
  public function login()
  {
    if ($this->validate()) {
      $user = $this->getUser();

      if ($user) {
        return Yii::$app->user->login($user, $this->rememberMe ? 3600 * 24 * 30 : 0);
      }
    }
    return false;
  }

  /**
   * @return User|null
   */
  public function getUser()
  {
    if ($this->_user === false) {
      $this->_user = User::findOne(['email' => $this->email]);
    }

    return $this->_user;
  }
}
