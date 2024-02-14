<?php
use portalium\menu\Module;
use kartik\color\ColorInput;
use portalium\menu\models\Menu;
use portalium\theme\helpers\Html;
?>
<?= $form->field($model, 'icon')->textInput(['maxlength' => true]) ?>
    <?php
        if($menuModel->type == Menu::TYPE['mobile'])
            echo Html::a(Module::t('You can reach the number of the icon you will choose by clicking here.'), 'https://api.flutter.dev/flutter/material/Icons-class.html', ['target' => '_blank'], ['class' => 'control-label']) . '<br><br>';
        else if($menuModel->type == Menu::TYPE['web'])
            echo Html::a(Module::t('You can reach the number of the icon you will choose by clicking here.'), 'https://fontawesome.com/v4/icons/', ['target' => '_blank'], ['class' => 'control-label']) . '<br><br>';
    ?>
    <?= $form->field($model, 'iconSize')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'display')->radioList(\portalium\menu\models\MenuItem::getDisplayList()) ?>

    <?= $form->field($model, 'childDisplay')->radioList(\portalium\menu\models\MenuItem::getDisplayList())->label(Module::t('Child Display')) ?>

    <?= $form->field($model, 'placement')->radioList(\portalium\menu\models\MenuItem::getPlacementList())->label(Module::t('Placement')) ?>


    <?= $form->field($model, 'color')->widget(ColorInput::className(), [
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