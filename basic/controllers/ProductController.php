<?php


namespace app\controllers;

use Yii;
use app\models\AddProductForm;
use app\models\Category;
use app\models\Product;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\Response;

class ProductController extends Controller
{
    /**
     * Displays add product page.
     *
     * @throws $error if product addition to database is not successful
     * @return Response|string
     */
    public function actionAddProduct()
    {
        $model = new AddProductForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            $product = new Product();
            $product->attributes = [
                'name' => $model->name,
                'price' => $model->price,
                'quantity' => $model->quantity
            ];

            foreach($model->categoryIds as $categoryId) {
                $productCategory = new Category();

                $productCategory->id = $categoryId;

                $productCategories[] = $productCategory;
            }

            try {
                $this->addProduct($product, $productCategories);
            } catch (\Throwable $e) {
                throw $e;
            }

            return $this->refresh();
        } else {

            return $this->render('add-product', ['model' => $model]);
        }
    }

    /**
     * Adds a product to the database and adds appropriate rows in linking table to join
     * products to their categories.
     *
     * @param Product $product the populated product to be added to the database.
     * @param Category $productCategories selected categories of the product to be added to the database.
     *
     * @throws HttpException
     *      status 400: if the entries are invalid, product is already in database,
     *      or a category is not in the database
     *
     *      status 500: if the transaction did not succeed
     *
     * @return bool true/false if the transaction succeeded
     */
    public function addProduct($product, $productCategories)
    {
        if(!$product->validate()) {
            throw new HttpException(400, 'Invalid product entries.');
        } elseif($product->getProductIdByName()) {
            throw new HttpException(400,'Product already exists.');
        }

        foreach($productCategories as $category) {
            $valid = $category->validate();
            if ($valid) {
                if(!$category->checkCategoryExistsById()) {
                    throw new HttpException(400, 'Category does not exist.');
                }
                continue;
            }

            throw new HttpException(400, "Category $category->id is invalid.");
        }

        $db = Product::getDb();
        $transaction = $db->beginTransaction();

        try {
            $db->createCommand()
                ->insert('product', $product)
                ->execute();

            $productId = $product->getProductIdByName()->id;

            foreach($productCategories as $category) {
                $db->createCommand()
                    ->insert('productCategory', [
                                                'categoryId' => $category->id,
                                                'productId' => $productId
                                            ])
                    ->execute();
            }

            $transaction->commit();

        } catch (\Throwable $e) {
            $transaction->rollBack();
            throw new HttpException(500, 'Something went wrong. Try again later.');
        }

        return true;
    }
}