<?php


namespace app\models;

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

            ['name', 'match', 'pattern' => '/^[a-zA-Z0-9]{1,255}$/',
                'message' => 'Invalid category name.'],
            ['name', function ($attribute, $params, $validator) {
                if ($this->getCategoryByName() === NULL) {
                    $this->addError(
                        $attribute,
                        "Category $attribute does not exist in database."
                    );
                }
            }]
        ];
    }

    /**
     * Gets a list of all non-deleted categories from the database
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
     * Defines the category side of the category:product many:many relationship
     */
    public function getProducts()
    {
        return $this->hasMany(Product::class, ['id' => 'id']);
    }

    /**
     * Gets category from the database based on its name
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
