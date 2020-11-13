<?php


namespace app\controllers;

use Yii;
use app\models\CategoryList;
use app\models\Category;
use app\models\Product;
use yii\db\ActiveRecord;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\Response;

class ProductController extends Controller
{
    /*
     * Specifies access controls for controller actions
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
     * @throw HttpException:
     *      status 400 if validation of models is incorrect
     *      status 500 if there is a error when adding the product to the database
     *
     * @return Response|string
     */
    public function actionAddProduct()
    {
        if(Yii::$app->user->isGuest || Yii::$app->user->identity->getAuthKey() !== 'test100key') {
            $this->goHome();
        }

        $message = '';
        $code = NULL;

        $product = new Product();
        $categoryList = new CategoryList();

        try {
            if ($product->load(Yii::$app->request->post())
                && $product->validate()
                && $categoryList->load(Yii::$app->request->post())
                && $categoryList->validate()) {

                foreach ($categoryList->categoryNames as $categoryName) {
                    $category = Category::getCategoryByName($categoryName);

                    if ($category !== NULL) {
                        if ($category->validate()) {
                            $categories[] = $category;

                            continue;
                        }
                        $messageString = $this->concatErrorMessages($category);

                        throw new HttpException(400, $messageString);
                    }

                    throw new HttpException(
                        400,
                        "Category $categoryName does not exist in database.");
                }

                $this->addProducttoDB($product, $categories);
                $this->refresh();
            }

            $message = $this->concatErrorMessages($product);

            throw new HttpException(400, $message);

        } catch (\Throwable $e) {
            $message = $e->getMessage();
            $code = $e->getCode();
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
     * @throws HttpException status 500: if the transaction did not succeed
     *
     * @return bool true if the transaction succeeded
     */
    private function addProducttoDB($product, $categories)
    {
        $db = Product::getDb();
        $transaction = $db->beginTransaction();

        try {
            $product->save();

            $productId = Product::getProductIdByName($product->name);
            
            foreach($categories as $category) {
                $category->link('products', $productId);
            }

            $transaction->commit();

        } catch (\Throwable $e) {
            $transaction->rollBack();
            throw new HttpException(500, 'Something went wrong. Try again later.');
        }

        return true;
    }

    /**
     * Takes an ActiveRecord and converts any of its error messages to a single string.
     *
     * @param ActiveRecord $model
     *
     * @return string $message
     */
    private function concatErrorMessages($model) {
        $message = '';
        foreach($model->errors as $error) {
            $message .= $error[0] . "\n";
        };

        return $message;
    }
}
