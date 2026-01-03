<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * Signup form
 */
class SignupForm extends Model
{
  public $name;
  public $email; // У формі ми називаємо це email, але в БД це запишеться в login
  public $password;

  public function rules()
  {
    return [
      ['name', 'trim'],
      ['name', 'required'],
      ['name', 'string', 'min' => 2, 'max' => 255],

      ['email', 'trim'],
      ['email', 'required'],
      ['email', 'email'],
      ['email', 'string', 'max' => 255],

      // ВАЖЛИВО: перевіряємо унікальність по колонці 'login' у таблиці User
      ['email', 'unique', 'targetClass' => '\app\models\User', 'targetAttribute' => 'email', 'message' => 'Ця пошта вже зайнята.'],

      ['password', 'required'],
      ['password', 'string', 'min' => 6],
    ];
  }

  /**
   * Реєстрація користувача.
   */
  public function signup()
  {
    if (!$this->validate()) {
      return null;
    }

    $user = new User();
    $user->name = $this->name;
    $user->login = $this->email; // Записуємо email у поле login

    // Зберігаємо пароль як є, бо у вашій моделі User перевірка йде через === (без хешування)
    $user->password = $this->password;

    $user->image = 'default.jpg'; // Заглушка, щоб не було помилки
    $user->isAdmin = 0; // Звичайний користувач

    return $user->save() ? $user : null;
  }
}
