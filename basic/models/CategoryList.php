<?php


namespace app\models;

use app\components\validators\NameValidator;
use app\components\validators\PositiveIntegerValidator;
use yii\base\Model;

class CategoryList extends Model
{
    public $categoryNames;
    public $categoryIds;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            ['categoryIds', 'required'],

            ['categoryNames', 'each', 'rule' => [NameValidator::class]],
            ['categoryIds', 'each', 'rule' => [PositiveIntegerValidator::class]],
        ];
    }
}
