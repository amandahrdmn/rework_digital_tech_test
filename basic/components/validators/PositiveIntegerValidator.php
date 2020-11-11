<?php


namespace app\components\validators;

use yii\validators\Validator;

class PositiveIntegerValidator extends Validator
{
    /*
     * Validates chosen attribute as an integer of greater than 0
     *
     * @return bool of pass/fail validation
     */
    public function validateAttribute($model, $attribute)
    {
        if (gettype($model->$attribute) === 'int' && $model->$attribute > 0) {
            return true;
        }

        return false;
    }
}
