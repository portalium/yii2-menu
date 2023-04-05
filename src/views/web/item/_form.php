<?php

use yii\helpers\Html;
use portalium\menu\Module;
use portalium\theme\widgets\Panel;
use portalium\menu\models\MenuItem;
use portalium\theme\widgets\ActiveForm;
use portalium\theme\widgets\Tabs;

/* @var $this yii\web\View */
/* @var $model portalium\menu\models\MenuItem */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="menu-item-form">
    <?php
    $isNewRecord = (isset($model->isNewRecord) && $model->isNewRecord == 1) ? 1 : 0;
    ?>
    <?php $form = ActiveForm::begin(
        [
            'id' => 'menu-item-form',
            'fieldConfig' => [
                'horizontalCssClasses' => [
                    'label' => 'col-sm-3',
                    'wrapper' => 'col-sm-9',
                ],
                'labelOptions' => ['style' => 'margin-top: 10px;'],
            ],
        ]
    ); ?>

    <?php Panel::begin([
        'title' => (isset($model->isNewRecord) && $model->isNewRecord == 1) ? Module::t('Create Menu Item') : Module::t('Update Menu Item'),
        'actions' => [
            'header' => [],
            'footer' => [
                Html::button(Module::t('Save'), ['class' => 'btn btn-success create-menu-item', 'id' => 'create-menu-item'])
            ]
        ]
    ]) ?>
    <?php
        Tabs::begin([
            'items' => [
                [
                    'label' => Module::t('General'),
                    'content' => $this->render('_form-general', ['model' => $model, 'form' => $form, 'id_menu' => $id_menu]),
                    'active' => true,
                    'options' => ['style' => 'margin-top: 10px;']
                ],
                [
                    'label' => Module::t('Style'),
                    'content' => $this->render('_form-style', ['model' => $model, 'form' => $form, 'menuModel' => $menuModel]),
                    'options' => ['style' => 'margin-top: 10px;']

                ],
            ]
        ]);
    ?>
    <?php Tabs::end() ?>
    

    <?php Panel::end() ?>

    <?php ActiveForm::end(); ?>

</div>

<?php

$this->registerJs('
        $(document).ready(function(){
            $("#type").trigger("change");
            $("#menu-type").trigger("change");
            setTimeout(function(){
                $("#routeType-list").val("' . $model->routeType . '");
                $("#routeType-list").trigger("change");
                setTimeout(function(){
                    $("#route-list").val("' . str_replace('\\', '\\\\', $model->route ? $model->route : '') . '");
                    $("#route-list").trigger("change");
                    setTimeout(function(){
                        $("#model-list").val("' . $model->model . '");
                        $("#model-list").trigger("change");
                    }, 1000);
                }, 1000);
            }, 1000);
            $("#model-list-div").hide();
        });
        $("#routeType-list").change(function(){
            if($(this).val() == "routes"){
                $("#model-list-div").hide();
            }else if($(this).val() == "models"){
                $("#model-list-div").show();
            }else if($(this).val() == "action"){
                $("#model-list-div").hide();
            }
        });

        $("#type").change(function(){
            if($(this).val() == ' . MenuItem::TYPE["module"] . '){
                $("#module-list-div").show();
                $("#routeType-list-div").hide();
                $("#route-list-div").hide();
                $("#url-input-div").hide();
            }
            else if($(this).val() == ' . MenuItem::TYPE["route"] . '){
                $("#module-list-div").hide();
                $("#routeType-list-div").hide();
                $("#route-list-div").hide();
                $("#url-input-div").show();
            } else if($(this).val() == ' . MenuItem::TYPE["url"] . '){
                $("#module-list-div").hide();
                $("#routeType-list-div").hide();
                $("#route-list-div").hide();
                $("#url-input-div").show();
            }

            $("#model-list-div").hide();
            $("#module-list").trigger("change");
        });

        $("#routeType-list").change(function(){

            if($(this).val() == "widget"){
                //$("#module-list-div").hide();
                $("#model-list-div").hide();
                $("#route-list-div").show();
            }else if($(this).val() == "model"){
                //$("#module-list-div").show();
                $("#model-list-div").show();
                $("#route-list-div").show();
            }else if($(this).val() == "action"){
                //$("#module-list-div").show();
                $("#model-list-div").hide();
                $("#route-list-div").show();
            }
        });
        flag = 0;
        $(document).ajaxStop(function(){
            if(!' . $isNewRecord . '&& flag == 0){
                $("#routeType-list").val("' . $model->routeType . '");
                $("#routeType-list").trigger("change");
                setTimeout(function(){
                    $("#route-list").val("' . str_replace('\\', '\\\\', $model->route ? $model->route : '') . '");
                    setTimeout(function(){
                        $("#model-list").val("' . $model->model . '");
                    }, 1000);
                }, 1000);
                flag = 1;
            }
        });

        $("#module-list").trigger("change");

        $("#module-list").change(function(){
            if ($("#type").val() == ' . MenuItem::TYPE["module"] . ' && $(this).val() != ""){
                $("#routeType-list-div").show();
            }else{
                $("#routeType-list-div").hide();
            }
            
        });
        
        $("#create-menu-item").click(function (e) {
            e.preventDefault();
            var form = $("#menu-item-form");
            var data = form.serialize();
            // data insert output
            data += "&output=" + $("#nestable-output").val();
            $.ajax({
                type: "POST",
                url: form.attr("action"),
                data: data,
                success: function (response) {
                    //wait 1 second before reload pjax
                    $.pjax.reload({container: "#nestable-pjax"});
                    //sleep(1000);
                    setTimeout(function () {
                        $.pjax.reload({container: "#nestable2-pjax"});
                        //trigger expand all button
                        $("#expand-all").trigger("click");
                    }, 1000);
    
                }
            });
        });
    ');

?>