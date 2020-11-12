<?php


namespace app\controllers;

use Yii;
use app\models\CategoryList;
use app\models\Category;
use app\models\Product;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\Response;

class ProductController extends Controller
{
    /*
     * specifies access controls for controller actions
     */
    public function accessRules()
    {
        return [
            ['deny',
                'actions'=>array('add-product'),
                'users'=>array('*'),
            ],
            ['allow',
                'actions'=>array('add-product'),
                'roles'=>array('admin'),
            ]
        ];
    }

    /**
     * Displays add product page.
     *
     * @throws $error if product addition to database is not successful
     * @return Response|string
     */
    public function actionAddProduct()
    {
        if(Yii::$app->user->isGuest || Yii::$app->user->identity->getAuthKey() !== 'test100key') {
            return $this->goHome();
        }
        $product = new Product();
        $categoryNames = new CategoryList();

        if ($product->load(Yii::$app->request->post())
            && $categoryNames->load(Yii::$app->request->post()) && $categoryNames->validate()) {

            foreach($categoryNames->categoryNames as $categoryName) {
                $category = new Category();
                $category->name = $categoryName;

                $categories[] = $category;
            }

            try {
                $this->addProduct($product, $categories);
            } catch (\Throwable $e) {
                throw $e;
            }

            return $this->refresh();
        } else {

            return $this->render('add-product', ['product' => $product, 'categoryNames' => $categoryNames]);
        }
    }

    /**
     * Adds a product to the database and adds appropriate rows in linking table to join
     * products to their categories.
     *
     * @param Product $product the populated product to be added to the database.
     * @param Category $categories selected categories of the product to be added to the database.
     *
     * @throws HttpException
     *      status 400: if the entries are invalid, product is already in database,
     *      or a category is not in the database
     *
     *      status 500: if the transaction did not succeed
     *
     * @return bool true/false if the transaction succeeded
     */
    private function addProduct($product, $categories)
    {
        if(!$product->validate()) {
            throw new HttpException(400, 'Invalid product entries.');
        } elseif($product->getProductByName()) {
            throw new HttpException(400,'Product already exists.');
        }

        foreach($categories as $category) {
            if ($category->validate()) {
                if(!$category->getCategoryByName()) {
                    throw new HttpException(400, "Category $category->id does not exist.");
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

            $productId = $product->getProductByName()->id;

            foreach($categories as $category) {
                $categoryId=$category->getCategoryByName()->id;
                $db->createCommand()
                    ->insert('product_category', [
                        'categoryId' => $categoryId,
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