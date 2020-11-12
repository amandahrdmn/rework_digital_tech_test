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

        $message = '';
        $code = NULL;

        $product = new Product();
        $categoryList = new CategoryList();

        if ($product->load(Yii::$app->request->post()) && $product->validate()
            && $categoryList->load(Yii::$app->request->post()) && $categoryList->validate()) {

            foreach($categoryList->categoryNames as $categoryName) {
                $category = new Category();
                $category->name = $categoryName;

                $categories[] = $category;
            }

            try {
                $this->addProduct($product, $categories);
                $this->refresh();

            } catch (\Throwable $e) {
                $message = $e->getMessage();
                $code = $e->getCode();
            }
        } else {
            foreach($product->errors as $error) {
                $message .= $error[0] . "\n";
            };
            $code = 400;
        }

        return $this->render('add-product', [
            'product' => $product,
            'categoryList' => $categoryList,
            'message' => $message,
            'code' => $code
        ]);
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
        if($product->getProductByName()) {
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