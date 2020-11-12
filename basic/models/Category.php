<?php


namespace app\models;

use app\components\validators\NameValidator;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

class Category extends ActiveRecord
{
    public $name;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            ['name', 'required'],

            ['name', NameValidator::class],
        ];
    }

    /**
     * gets the list of all non-deleted categories from the database
     *
     * @return array associative array of categoryId => categoryName.
     */
    public static function getCategoryList()
    {
        $categoryList = Category::find()
            ->select(['id', 'name'])
            ->where(['deleted' => 0])
            ->orderBy('name')
            ->asArray()
            ->all();

        return ArrayHelper::map($categoryList, 'id', 'name');
    }

    /*
     * defines the category side of the category:product many:many relationship
     */
    public function getProducts()
    {
        return $this->hasMany(Product::class, ['id' => 'id']);
    }

    /**
     * Get category from the database via its name
     *
     * @return ActiveRecord $category with its id only or nothing if it does not exist.
     */
    public function getCategoryByName()
    {
        return $this::find()
            ->select('id')
            ->where(['name' => $this->name])
            ->one();
    }
}
