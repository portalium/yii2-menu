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
            'id' => 'menu-move-item-form',
            'options' => ['style' => 'padding-top: 10px;'],
            'action' => Url::to(['/menu/item/move']),
            'fieldConfig' => [
                'horizontalCssClasses' => [
                    'label' => 'col-sm-3',
                    'wrapper' => 'col-sm-9',
                ],
                'labelOptions' => ['style' => 'margin-top: 10px;'],
            ],
        ]);
        echo $form->field($model, 'id_menu', ['options' => ['id' => 'menu-list-div']])->dropDownList($menuArray, ['id' => 'menu-list', 'prompt' => 'Select Menu'])->label('Menu');

        echo $form->field($model, 'id_parent', ['options' => ['id' => 'parent-list-div']])->widget(DepDrop::classname(), [
            'options' => ['id' => 'parent-list'],
            'pluginOptions' => [
                'depends' => ['menu-list'],
                'placeholder' => Module::t('Select...'),
                'url' => Url::to(['/menu/item/parent-list']),
                'paramsBase' => [
                    Yii::$app->request->csrfParam => Yii::$app->request->csrfToken,
                ]
            ]
        ])->label('Parent');
        ActiveForm::end();
    ?>

</div>

<?php
$this->registerJs('
    $(document).ajaxSend(function(event, jqxhr, settings) {
        if (settings.type == "POST") {
            settings.data = settings.data + "&' . Yii::$app->request->csrfParam . '=' . Yii::$app->request->csrfToken . '";
        }
    });
');
?>