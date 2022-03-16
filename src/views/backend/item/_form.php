<?php

use yii\helpers\Url;
use yii\helpers\Html;
use portalium\menu\Module;
use kartik\depdrop\DepDrop;
use yii\widgets\ActiveForm;
use portalium\theme\widgets\Panel;
use portalium\menu\models\MenuItem;
/* @var $this yii\web\View */
/* @var $model portalium\menu\models\MenuItem */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="menu-item-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php Panel::begin([
        'title' => ($model->isNewRecord) ? Module::t('Create Menu Item') : Module::t('Update Menu Item'),
        'actions' => [
            'header' => [
                Html::submitButton(Module::t('Save'), ['class' => 'btn btn-success'])
            ]
        ]
    ]) ?>

    <?= $form->field($model, 'label')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'slug')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'type')->dropDownList(MenuItem::getTypes()) ?>

    <?= $form->field($model, 'icon')->textInput(['maxlength' => true]) ?>
    
    <?php

        echo $form->field($model, 'module')->dropDownList(MenuItem::getModuleList(), ['id' => 'module-list']);

        echo $form->field($model, 'module')->widget(DepDrop::classname(), [
            'options' => ['id' => 'module-action-list'],
            'pluginOptions' => [
                'depends' => ['module-list'],
                'placeholder' => Module::t('Select...'),
                'url' => Url::to(['/menu/item/controller'])
            ]
        ]);

        //model
        echo $form->field($model, 'module')->widget(DepDrop::classname(), [
            'options' => ['id' => 'model-action-list'],
            'pluginOptions' => [
                'depends' => ['module-list'],
                'placeholder' => Module::t('Select...'),
                'url' => Url::to(['/menu/item/model'])
            ]
        ]);

        //action
        echo $form->field($model, 'module')->widget(DepDrop::classname(), [
            'options' => ['id' => 'action-list'],
            'pluginOptions' => [
                'depends' => ['model-action-list'],
                'placeholder' => Module::t('Select...'),
                'url' => Url::to(['/menu/item/data'])
            ]
        ]);


    ?>
    
    <?php Panel::end() ?>

    <?php ActiveForm::end(); ?>

</div>
