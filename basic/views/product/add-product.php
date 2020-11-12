<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $product app\models\Product */
/* @var $categoryList app\models\CategoryList array*/
/* @var string $message sent from the ProductController; contents dependent on dynamic errors*/

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use \app\models\Category;

$this->title = 'Add Product';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-add-product">
    <h1>
        <?= Html::encode($this->title); ?></h1>

    <p class="page_explanation">
        This is the form for adding a product. Please note that you cannot add previously added products.
    </p>

    <?php if($message !== ''): ?>
            <div class='alert alert-danger'>
                <?php echo nl2br(Html::encode($message)) ?>
            </div>
    <?php endif; ?>

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
                    'maxlength' => 16,
                    'id' => 'priceField'
                ])
            ?>

            <?= $form->field($product, 'quantity')
                ->textInput(['type' => 'text',
                    'maxlength' => 11,
                    'id' => 'quantityField'
                ])
            ?>

            <?= $form->field($categoryList, 'categoryNames')
                ->checkboxList(
                    Category::getCategoryList(),
                    ['item' => function($index, $label, $name, $checked, $value) {
                        return "<div class='checkbox'>
                                    <label>
                                        <input type='checkbox' {$checked}
                                                name='{$name}'
                                                value='{$label}'>{$label}"
                                    ."</label>
                                </div>";
                    }]
                );

            ?>

            <div class="form-group">
                <?= Html::submitButton('Submit', [
                    'class' => 'btn btn-primary',
                    'name' => 'add-product-button'
                ]) ?>
                <a class="btn btn-danger" href="../index.php">Back</a>
            </div>

            <?php ActiveForm::end(); ?>

        </div>
    </div>
</div>
