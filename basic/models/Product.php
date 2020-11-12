<?php


namespace app\models;

use app\components\validators\NameValidator;
use app\components\validators\PositiveIntegerValidator;
use app\components\validators\PriceValidator;
use yii\db\ActiveRecord;

class Product extends ActiveRecord
{
    public $name;
    public $price;
    public $quantity;

    /**
     * initialises all Products with a default value of deleted = 0
     */
    public function init()
    {
        parent::init();

        $this->deleted = 0;
    }

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['name', 'price', 'quantity'], 'required'],

            ['name', NameValidator::class],
            ['price', PriceValidator::class],
            ['quantity', PositiveIntegerValidator::class],
        ];
    }

    /*
     * defines the product side of the category:product many:many relationship
     */
    public function getCategories()
    {
        return $this->hasMany(Category::class, ['product_id' => 'id']);
    }

    /**
     * Gets the product id from the database based on its name
     *
     * @return ActiveRecord $product with its id only or nothing if it does not exist.
     */
    public function getProductIdByName()
    {
        return $this::find()
            ->select('id')
            ->where(['name' => $this->name])
            ->one();
    }
}
