<?php

namespace App\Http\Forms;

use App\Core\App;

class FormValidator {
    /* Global error store, to store all validation errors that need to be returned. */
    protected static $errors = [];

    /*  validate($attributes):
            This function links the validator to form validation, and provides meaningfull errors that can serve as user feedback.
                $attributes (Assoc Arr) - The data that needs to be validated, usually the entire POST.
                $stringKeys_# (Array)   - The key names i want to check, to combine similar validations.
            
            Return Value:
                On failure - String.
                On success - Boolean.
     */
    public static function validate($attributes) {
        foreach($attributes as $key => $value) {
            $stringKeys_1 = ['wachtwoord', 'gebr-naam'];
            $stringKeys_2 = ['naam', 'autheur'];

            if($key === 'email' && !App::resolve('validator')::email($value)) {
                self::$errors['email'] = App::resolve('errors')->getError('validation', 'user-mail');
            }

            if($key == 'wachtwoord' && !App::resolve('validator')::pwSecure($value)) {
                self::$errors['secure'] = App::resolve('errors')->getError('validation', 'pw-sec');
            }

            if(in_array($key, $stringKeys_1) && !App::resolve('validator')::string($value, 7, 35)) {
                if($key === 'wachtwoord') {
                    $complex = App::resolve('validator')::complex($value);
                    if(!$complex) {
                        self::$errors['password'] = App::resolve('errors')->getError('validation', 'user-pw');
                    }
                } else {
                    self::$errors['gebr-naam'] = App::resolve('errors')->getError('validation', 'user-name');
                }
            }

            if(in_array($key, $stringKeys_2) && !App::resolve('validator')::string($value, 7, 50)) {
                if($key === 'naam') {
                    self::$errors['naam'] = App::resolve('errors')->getError('validation', 'naam-input');
                } else {
                    self::$errors['maker'] = App::resolve('errors')->getError('validation', 'autheur');
                }
            }

            if($key === 'opmerking' && !App::resolve('validator')::string($value, 1, 254)) {
                self::$errors['opmerking'] = App::resolve('errors')->getError('validation', 'opmerking');
            }

            /* POST input data are string values, even though isbn is technically a int value when stored. */
            if($key === 'isbn' && !App::resolve('validator')::string($value, 10, 13)) {
                self::$errors['isbn'] = App::resolve('erros')->getError('validation', 'isbn');
            }
        }

        $instance = new static ($attributes);

        if($instance->failed()) {
            return $instance->errors();
        }

        return TRUE;
    }

    /*  failed():
            A little helper function, to see if any error where set during validation.

            Return Value: Int.
     */
    public function failed() {
        return count(self::$errors);
    }

    /* errors():
            A lttle helper function, to return the errors that were set during validation.

            Return Value: Associateve Array.
     */
    public function errors() {
        return self::$errors;
    }
}