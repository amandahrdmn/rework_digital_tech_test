<?php


namespace app\models;

use yii\base\Model;

/**
 * AddProductForm is the model behind the add product form.
 */
class AddProductForm extends Model
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
        ];
    }
}
