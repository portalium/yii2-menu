<?php

use yii\helpers\Html;
use portalium\theme\widgets\ActiveForm;
use portalium\menu\Module;
/* @var $this yii\web\View */
/* @var $model portalium\menu\models\MenuItemSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="menu-item-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id_item') ?>

    <?= $form->field($model, 'label') ?>

    <?= $form->field($model, 'slug') ?>

    <?= $form->field($model, 'icon') ?>

    <?php // echo $form->field($model, 'id_menu') ?>

    <?php // echo $form->field($model, 'date_create') ?>

    <?php // echo $form->field($model, 'date_update') ?>

    <div class="form-group">
        <?= Html::submitButton(Module::t('Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Module::t('Reset'), ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
