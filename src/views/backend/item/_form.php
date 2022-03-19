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

    <?= $form->field($model, 'type')->dropDownList(MenuItem::getTypes(), ['id' => 'type']) ?>

    <?= $form->field($model, 'icon')->textInput(['maxlength' => true]) ?>
    
    <?php

        echo $form->field($model, 'module', ['options'=>['id' => 'module-list-div']])->dropDownList(MenuItem::getModuleList(), ['id' => 'module-list', 'prompt' => 'Select Module']);

        echo $form->field($model, 'routeType', ['options'=>['id' => 'routeType-list-div']])->widget(DepDrop::classname(), [
            'options' => ['id' => 'routeType-list'],
            'pluginOptions' => [
                'depends' => ['module-list'],
                'placeholder' => Module::t('Select...'),
                'url' => Url::to(['/menu/item/route-type'])
            ]
        ]);

        echo $form->field($model, 'route', ['options'=>['id' => 'route-list-div']])->widget(DepDrop::classname(), [
            'options' => ['id' => 'route-list'],
            'pluginOptions' => [
                'depends' => ['module-list', 'routeType-list'],
                'placeholder' => Module::t('Select...'),
                'url' => Url::to(['/menu/item/route'])
            ]
        ]);

        echo $form->field($model, 'model', ['options'=>['id' => 'model-list-div']])->widget(DepDrop::classname(), [
            'options' => ['id' => 'model-list'],
            'pluginOptions' => [
                'depends' => ['module-list', 'routeType-list', 'route-list'],
                'placeholder' => Module::t('Select...'),
                'url' => Url::to(['/menu/item/model'])
            ]
        ]);

        echo $form->field($model, 'url', ['options'=>['id' => 'url-input-div']])->textInput(['rows' => 6]);

        echo $form->field($model, 'name_auth', ['options'=>['id' => 'name-auth-input-div']])->dropDownList(MenuItem::getAuthList(), ['id' => 'name-auth-input', 'prompt' => 'Select Auth', "options" => ['role' => ['disabled' => true], 'permission' => ['disabled' => true]]]);

    ?>
    
    <?php Panel::end() ?>

    <?php ActiveForm::end(); ?>

</div>

<?php 
    $this->registerJs('
        $(document).ready(function(){
            $("#type").trigger("change");

            $("#model-list-div").hide();
        });
        $("#routeType-list").change(function(){
            if($(this).val() == "routes"){
                $("#model-list-div").hide();
            }else{
                $("#model-list-div").show();
            }
        });

        $("#type").change(function(){
            if($(this).val() == '.MenuItem::TYPE["module"].'){
                $("#module-list-div").show();
                $("#routeType-list-div").show();
                $("#route-list-div").show();
                $("#url-input-div").hide();

            }else{
                $("#module-list-div").hide();
                $("#routeType-list-div").hide();
                $("#route-list-div").hide();
                $("#url-input-div").show();
            }

            $("#model-list-div").hide();
        });
    ');

?>
