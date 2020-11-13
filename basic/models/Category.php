<?php


namespace app\models;

use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

class Category extends ActiveRecord
{
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
                if ($this->checkCategoryExistsByName() === false) {
                    $this->addError(
                        $attribute,
                        "Category $attribute does not exist in database."
                    );
                }
            }]
        ];
    }

    /**
     * Defines category side of product-category many-to-many relationship
     */
    public function getProducts()
    {
        return $this->hasMany(Product::class, ['id' => 'product_id'])
            ->viaTable('product_category', ['category_id' => 'id']);
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

    /**
     * Gets category from the database based on its name
     *
     * @return ActiveRecord $category or nothing if it does not exist.
     */
    public static function getCategoryByName($name)
    {
        return Category::find()
            ->select(['id', 'name'])
            ->where(['name' => $name])
            ->one();
    }

    /**
     * Checks if category exists in the database based on its name
     *
     * @return bool of true/false if it does/not exist.
     */
    public function checkCategoryExistsByName()
    {
        $category = $this::find()
            ->select('id')
            ->where(['name' => $this->name])
            ->one();
        if($category !== NULL) {
            return true;
        }
        return false;
    }
}
