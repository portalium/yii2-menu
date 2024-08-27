<?php

use portalium\menu\models\Menu;
use yii\helpers\Html;
use portalium\theme\widgets\ActiveForm;
use portalium\menu\Module;
use portalium\theme\widgets\Panel;
/* @var $this yii\web\View */
/* @var $model portalium\menu\models\Menu */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="menu-form">


    <?php $form = ActiveForm::begin(); ?>
    <?php Panel::begin([
        'title' => ($model->isNewRecord) ? Module::t('Create Menu') : Module::t('Update Menu'),
        'actions' => [
            'header' => [
            ],
            'footer' => [
                Html::submitButton(Module::t( 'Save'), ['class' => 'btn btn-success']),
            ]
        ]
    ]) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'slug')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'type')->dropDownList(Menu::getTypes()) ?>

    <?= $form->field($model, 'direction')->dropDownList(Menu::getDirections()) ?>
    
    <?= $form->field($model, 'placement')->dropDownList(Menu::getPlacements()) ?>

    <?php Panel::end() ?>

    <?php ActiveForm::end(); ?>

</div>
