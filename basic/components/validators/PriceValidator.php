<?php


namespace app\components\validators;

use \yii\validators\Validator;

class PriceValidator extends Validator
{
    /*
     * Validates chosen attribute based on a regular expression:
     * a string of numbers 1-12 characters long + a . + a string of numbers 0-4 characters long
     *
     * @return bool of pass/fail validation
     */
    public function validateAttribute($model, $attribute)
    {
        return (gettype($model->$attribute) === 'float' &&
            preg_match('/^[0-9]{1,12}(\.[0-9]{0,4})?$/', $model->$attribute));
    }
}
