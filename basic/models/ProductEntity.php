<?php


namespace app\models;

use yii\base\Model;

/**
 * AddProductForm is the model behind the add product form.
 */
class ProductEntity extends Model
{
    public $productName;
    public $price;
    public $quantity;
    public $categories;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // name, email, subject and body are required
            [['productName', 'price', 'quantity', 'categories'], 'required'],
            [['productName', 'price', 'quantity', 'categories'], 'trim'],

            ['productName', 'match', 'pattern'=>'/^[a-zA-Z0-9]{1,255}$/'],
            ['price', 'match', 'pattern'=>'/^[0-9]{1,12}(\.[0-9]{0,4})?$/'],
            ['quantity', 'integer', 'min' => 0],
            ['categories', 'each', 'rule' => ['integer', 'min' => 0]]
        ];
    }
}
