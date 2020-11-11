<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;

$this->title = 'Add Product';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-add-product">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        This is the form for adding a product. Please note that you cannot add previously added products.
    </p>

    <div class="row">
        <div class="col-xs-12">

            <?php $form = ActiveForm::begin(['id' => 'add-product-form']); ?>

            <?= $form->field($model, 'productName')->textInput(['autofocus' => true]) ?>

            <?= $form->field($model, 'price')->textInput(['type' => 'text',
                'maxlength' => 13,
                'id' => 'priceField',
            ])?>

            <?= $form->field($model, 'quantity')->textInput(['type' => 'text',
                'maxlength' => 11,
                'id' => 'quantityField',
            ]) ?>

            <?= $form->field($model, 'categories')->checkboxList([
                1 => 'checkbox 1',
                2 => 'checkbox 2',
                3 => 'checkbox 2',
                4 => 'checkbox 2',
                5 => 'checkbox 2'
            ]);
            ?>

            <div class="form-group">
                <?= Html::submitButton('Submit', ['class' => 'btn btn-primary', 'name' => 'add-product-button']) ?>
                <a class="btn btn-danger" href="index.php">Back</a>
            </div>

            <?php ActiveForm::end(); ?>

        </div>
    </div>
</div>
