<?php


namespace app\models;

use app\components\validators\NameValidator;
use app\components\validators\PositiveIntegerValidator;
use app\components\validators\PriceValidator;
use yii\base\Model;

class AddProductForm extends Model
{
    public $name;
    public $price;
    public $quantity;
    public $categoryIds;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // all attributes except categoryNames are required
            [['name', 'price', 'quantity', 'categoryIds'], 'required'],
            [['name', 'price', 'quantity'], 'trim'],

            ['name', NameValidator::class],
            ['price', PriceValidator::class],
            ['quantity', PositiveIntegerValidator::class],
            ['categoryIds', 'each', 'rule' => [PositiveIntegerValidator::class]],
        ];
    }
}
