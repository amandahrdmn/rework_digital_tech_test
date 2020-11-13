<?php


namespace app\models;

use yii\db\ActiveRecord;

class Product extends ActiveRecord
{
    /**
     * Initialises all Products with a default value of deleted = 0
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
            ['name', function ($attribute, $params, $validator) {
                if ($this->checkProductExistsByName() === true) {
                    $this->addError(
                        $attribute,
                        'Product already exists in database.');
                }
            }]
        ];
    }

    /**
     * Defines product side of product-category many-to-many relationship
     */
    public function getCategories()
    {
        return $this->hasMany(Category::class, ['id' => 'category_id'])
            ->viaTable('product_category', ['product_id' => 'id']);
    }

    /**
     * Gets product from the database based on its name
     *
     * @return ActiveRecord $product or nothing if it does not exist.
     */
    public static function getProductIdByName($name)
    {
        return Product::find()
            ->select('id')
            ->where(['name' => $name])
            ->one();
    }

    /**
     * Checks if product exists in the database based on its name
     *
     * @return bool of true/false if it does/not exist.
     */
    public function checkProductExistsByName()
    {
        $category = $this::find()
            ->select(['id'])
            ->where(['name' => $this->name])
            ->one();
        if($category !== NULL) {
            return true;
        }
        return false;
    }
}
