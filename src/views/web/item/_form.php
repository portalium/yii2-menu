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
                Html::button(Module::t('Save'), ['class' => 'btn btn-success create-menu-item', 'id' => 'create-menu-item']),
                Html::tag('div', '', ['class' => 'spinner-border text-primary', 'role' => 'status', 'style' => 'display:none; margin-left: 3px; margin-bottom: -10px;', 'id' => 'spinner'])
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
            $("#model-list-div").hide();
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
            if($(this).val() == "routes"){
                $("#model-list-div").hide();
            }else if($(this).val() == "models"){
                $("#model-list-div").show();
            }else if($(this).val() == "action"){
                $("#model-list-div").hide();
            }
            if($(this).val() == "widget"){
                $("#model-list-div").hide();
                $("#route-list-div").show();
            }else if($(this).val() == "model"){
                $("#model-list-div").show();
                $("#route-list-div").show();
            }else if($(this).val() == "action"){
                $("#model-list-div").hide();
                $("#route-list-div").show();
            }
            setTimeout(function(){
                $("#route-list").val("' . str_replace('\\', '\\\\', $model->route ? $model->route : '') . '");
                setTimeout(function(){
                    $("#model-list").val("' . $model->model . '");
                    $("#drop-menu-form").show();
                    $("#spinner-div-form").hide();
                    $(".edit-item").attr("disabled", false);
                    $(".clone-item").attr("disabled", false);
                    $(".delete-item").attr("disabled", false);
                    $(".create-item").attr("disabled", false);
                    $(".move-item").attr("disabled", false);
                    $(".dd-handle-button").attr("disabled", false); 
                }, 1500);
            }, 1500);
                

        });


        $("#module-list").change(function(){
            if ($("#type").val() == ' . MenuItem::TYPE["module"] . ' && $(this).val() != ""){
                $("#routeType-list-div").show();
            }else{
                $("#routeType-list-div").hide();
            }
            setTimeout(function(){
                $("#routeType-list").val("' . $model->routeType . '");
                $("#routeType-list").trigger("change");
            }, 1500);
        });
        
        $("#create-menu-item").click(function (e) {
            disabledButton();
            e.preventDefault();
            var form = $("#menu-item-form");
            var data = form.serialize();
            // data insert output
            data += "&output=" + $("#nestable-output").val();
            $.ajax({
                type: "POST",
                url: form.attr("action"),
                data: data,
                beforeSend: function () {
                    $("#spinner").show();
                },
                success: function (response) {
                $.pjax.reload({ container: "#nestable-pjax" }).done(function () {
                    $.pjax.reload({ container: "#nestable2-pjax" }).done(function () {
                        $("#expand-all").trigger("click");
                        $("#spinner").hide();
                    });
                });
                }
            });
        });
        function disabledButton() {
            $("#drop-menu-form").hide();
            $("#spinner-div-form").show();
            $(".edit-item").attr("disabled", true);
            $(".clone-item").attr("disabled", true);
            $(".delete-item").attr("disabled", true);
            $(".create-item").attr("disabled", true);
            $(".move-item").attr("disabled", true);
            $(".dd-handle-button").attr("disabled", true);
        }
    ');

?>