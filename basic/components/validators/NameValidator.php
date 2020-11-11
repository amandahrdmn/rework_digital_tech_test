<?php


namespace app\components\validators;

use yii\validators\Validator;

class NameValidator extends Validator
{
    /*
     * Validates chosen attribute based on a regular expression:
     * a string of numbers and letters between 1 and 255 characters long
     *
     * @return bool of pass/fail validation
     */
    public function validateAttribute($model, $attribute)
    {
        return preg_match('/^[a-zA-Z0-9]{1,255}$/', $model->$attribute);
    }
}
