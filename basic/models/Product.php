<?php


namespace app\models;

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

            ['name', 'match', 'pattern' => '/^[a-zA-Z0-9]{1,255}$/',
                'message' => 'Invalid product name.'],
            ['price', 'match', 'pattern' => '/^[0-9]{1,12}(\.[0-9]{0,4})?$/',
                'message' => 'Invalid product price.'],
            ['quantity', 'match', 'pattern' => '/^[0-9]{1,11}$/',
                'message' => 'Invalid product quantity.'],
        ];
    }

    /*
     * defines the product side of the category:product many:many relationship
     */
    public function getCategories()
    {
        return $this->hasMany(Category::class, ['id' => 'id']);
    }

    /**
     * Gets the product id from the database based on its name
     *
     * @return ActiveRecord $product with its id only or nothing if it does not exist.
     */
    public function getProductByName()
    {
        return $this::find()
            ->select('id')
            ->where(['name' => $this->name])
            ->one();
    }
}
