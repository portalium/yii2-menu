<?php

use yii\helpers\Url;

use portalium\menu\Module;
use kartik\depdrop\DepDrop;
use portalium\theme\widgets\ActiveForm;


/* @var $this yii\web\View */
/* @var $model portalium\menu\models\MenuItem */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="menu-item-form">

    <?php
    $form = ActiveForm::begin([
        'id' => 'menu-delete-item-form',
        'options' => ['style' => 'padding-top: 10px;'],
        'action' => Url::to(['/menu/item/delete']),
        'fieldConfig' => [
            'horizontalCssClasses' => [
                'label' => 'col-sm-3',
                'wrapper' => 'col-sm-9',
            ],
            'labelOptions' => ['style' => 'margin-top: 10px;'],
        ],
    ]);

    echo $form->field($model, 'delete_type')->dropDownList(['delete' => 'Delete With All Sub Items', 'delete-and-move-sub-items' => 'Delete And Move Sub Items'], ['id' => 'delete-type', 'class' => 'form-control', 'style' => 'width: 100%;']);

    echo \portalium\theme\widgets\Html::beginTag('div', ['id' => 'delete-and-move-sub-items-div', 'style' => 'display: none;']);
    echo $form->field($model, 'id_menu', ['options' => ['id' => 'menu-list-delete-div']])->dropDownList($menuArray, ['id' => 'menu-list-delete', 'prompt' => 'Select Menu'])->label('Menu');

    echo $form->field($model, 'id_parent', ['options' => ['id' => 'parent-list-delete-div']])->widget(DepDrop::classname(), [
        'options' => ['id' => 'parent-list-delete'],
        'pluginOptions' => [
            'depends' => ['menu-list-delete'],
            'placeholder' => Module::t('Select...'),
            'url' => Url::to(['/menu/item/parent-list']),
            'paramsBase' => [
                Yii::$app->request->csrfParam => Yii::$app->request->csrfToken,
            ]
        ]
    ])->label('Parent');
    echo \portalium\theme\widgets\Html::endTag('div');
    ActiveForm::end();
    ?>

</div>

<?php
$this->registerJs(
    "
            $('#delete-type').on('change', function() {
                if($(this).val() == 'delete-and-move-sub-items') {
                    $('#delete-and-move-sub-items-div').show();
                } else {
                    $('#delete-and-move-sub-items-div').hide();
                }
            });
        "
);

$this->registerJs('
    $(document).ajaxSend(function(event, jqxhr, settings) {
        if (settings.type == "POST") {
            settings.data = settings.data + "&' . Yii::$app->request->csrfParam . '=' . Yii::$app->request->csrfToken . '";
        }
    });
');

?>