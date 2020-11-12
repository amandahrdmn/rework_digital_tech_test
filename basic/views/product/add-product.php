<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $product app\models\Product */
/* @var $categories app\models\Category array*/

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use \app\models\Category;

$this->title = 'Add Product';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-add-product">
    <h1>
        <?= Html::encode($this->title); ?></h1>

    <p>
        This is the form for adding a product. Please note that you cannot add previously added products.
    </p>

    <div class="row">
        <div class="col-xs-12">

            <?php $form = ActiveForm::begin(['id' => 'add-product-form']); ?>

            <?= $form->field($product, 'name')
                ->textInput(['autofocus' => true,
                    'id' => 'nameField'
                ])
            ?>

            <?= $form->field($product, 'price')
                ->textInput(['type' => 'text',
                    'maxlength' => 13,
                    'id' => 'priceField'
                ])
            ?>

            <?= $form->field($product, 'quantity')
                ->textInput(['type' => 'text',
                    'maxlength' => 11,
                    'id' => 'quantityField'
                ])
            ?>

            <?= $form->field($categories, 'categoryIds')
                ->checkboxList(Category::getCategoryList());
            ?>

            <div class="form-group">
                <?= Html::submitButton('Submit', [
                        'class' => 'btn btn-primary',
                        'name' => 'add-product-button'
                ]) ?>
                <a class="btn btn-danger" href="../site/index.php">Back</a>
            </div>

            <?php ActiveForm::end(); ?>

        </div>
    </div>
</div>
