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
    <?= Html::a(Module::t('You can reach the number of the Icon you will choose by clicking here.'), 'https://api.flutter.dev/flutter/material/Icons-class.html', ['target' => '_blank'], ['class' => 'control-label']).'<br><br>' ?>
    <?= $form->field($model, 'iconSize')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'color')->widget(\kartik\color\ColorInput::className(), [
        'options' => ['placeholder' => Module::t('Select Color ...')],
        'pluginOptions' => [
            'showInput' => true,
            'showInitial' => true,
            'showPalette' => true,
            'showSelectionPalette' => true,
            'showAlpha' => true,
            'preferredFormat' => 'rgb',
            'palette' => [
                [
                    "white", "black", "grey", "silver", "gold", "brown",
                ],
                [
                    "red", "orange", "yellow", "indigo", "maroon", "magenta",
                ],
                [
                    "blue", "light-blue", "cyan", "teal", "green", "lime", "olive",
                ],
                [
                    "purple", "pink", "deep-purple", "light-green", "lime", "brown",
                ],
            ],
        ]
    ]) ?>

    <?php

        echo $form->field($model, 'module', ['options'=>['id' => 'module-list-div']])->dropDownList(MenuItem::getModuleList(), ['id' => 'module-list', 'prompt' => 'Select Module']);

        echo $form->field($model, 'menuType')->dropDownList(['web' => 'Web', 'mobile' => 'Mobile'], ['id' => 'menu-type', 'prompt' => 'Select Menu Type'])->label('Menu Type');

        echo $form->field($model, 'routeType', ['options'=>['id' => 'routeType-list-div']])->widget(DepDrop::classname(), [
            'options' => ['id' => 'routeType-list'],
            'pluginOptions' => [
                'depends' => ['menu-type', 'module-list'],
                'placeholder' => Module::t('Select...'),
                'url' => Url::to(['/menu/item/route-type'])
            ]
        ]);

        echo $form->field($model, 'route', ['options'=>['id' => 'route-list-div']])->widget(DepDrop::classname(), [
            'options' => ['id' => 'route-list'],
            'pluginOptions' => [
                'depends' => ['menu-type', 'module-list', 'routeType-list'],
                'placeholder' => Module::t('Select...'),
                'url' => Url::to(['/menu/item/route'])
            ]
        ]);

        echo $form->field($model, 'model', ['options'=>['id' => 'model-list-div']])->widget(DepDrop::classname(), [
            'options' => ['id' => 'model-list'],
            'pluginOptions' => [
                'depends' => ['menu-type', 'module-list', 'routeType-list', 'route-list'],
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
            $("#menu-type").trigger("change");
            setTimeout(function(){
                console.log("selam");
                $("#routeType-list").val("'.$model->routeType.'");
                $("#routeType-list").trigger("change");
                setTimeout(function(){
                    $("#route-list").val("'.$model->route.'");
                    $("#route-list").trigger("change");
                    setTimeout(function(){
                        $("#model-list").val("'.$model->model.'");
                        $("#model-list").trigger("change");
                    }, 1000);
                }, 1000);
            }, 1000);
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
        $("#routeType-list").change(function(){
            if($(this).val() == "route"){
                //$("#module-list-div").hide();
                
                $("#model-list-div").hide();
            }else{
                //$("#module-list-div").show();
                
                $("#model-list-div").show();
            }
        });
    ');

?>
