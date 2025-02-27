<?php

namespace App\Http\Forms;

use App\Core\App;

class FormValidator {
    protected static $errors = [];

    public static function validate($attributes) {
        foreach($attributes as $key => $value) {
            if($key === 'email' && !App::resolve('validator')::email($value)) {
                self::$errors['email'] = App::resolve('errors')->getError('validation', 'user-mail');
            }

            if($key == 'wachtwoord' && !App::resolve('validator')::pwSecure($value)) {
                self::$errors['secure'] = App::resolve('errors')->getError('validation', 'pw-sec');
            }

            if($key === 'wachtwoord' && !App::resolve('validator')::string($value, 7, 35)) {
                self::$errors['password'] = App::resolve('errors')->getError('validation', 'user-pw');
            }

            if($key === 'gebr-naam' && !App::resolve('validator')::string($value, 5, 25)) {
                self::$errors['gebr-naam'] = App::resolve('errors')->getError('validation', 'user-name');
            }

            if($key === 'naam' && !App::resolve('validator')::string($value, 5, 50)) {
                self::$errors['naam'] = App::resolve('errors')->getError('validation', 'naam-input');
            }

            if($key === 'autheur' && !App::resolve('validator')::string($value, 7, 50)) {
                self::$errors['makers'] = App::resolve('errors')->getError('validation', 'autheur');
            }

            if($key === 'opmerking' && !App::resolve('validator')::string($value, 1, 254)) {
                self::$errors['opmerking'] = App::resolve('errors')->getError('validation', 'opmerking');
            }
        }

        $instance = new static ($attributes);

        if($instance->failed()) {
            return $instance->errors();
        }

        return;
    }

    public function failed() {
        return count(self::$errors);
    }

    public function errors() {
        return self::$errors;
    }
}