<?php


namespace app\models;

use yii\base\Model;

class CategoryList extends Model
{
    public $categoryNames;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            ['categoryNames', 'required'],
        ];
    }
}
