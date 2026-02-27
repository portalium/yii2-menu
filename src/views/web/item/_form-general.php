<?php

use Yii;
use yii\helpers\Url;
use portalium\menu\Module;
use portalium\menu\models\MenuItem;
use yii\web\NotFoundHttpException;
use portalium\base\Exception;
use portalium\menu\bundles\MenuDepDropAsset;
use kartik\select2\Select2;

/**
 * EN: Registering the asset bundle for cascade dropdown logic (depdropMenu.js).
 */
MenuDepDropAsset::register($this);
?>

<?= $form->field($model, 'label')->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'slug')->textInput(['maxlength' => true]) ?>

<?php 
/**
 * EN: Optimized Parent List: Uses Eager Loading behind the scenes to prevent N+1 queries.
 */ ?>
<?= $form->field($model, 'id_parent')->dropDownList(MenuItem::getParents($id_menu), ['id' => 'id_item'])->label(Module::t('Parent')) ?>


<?= $form->field($model, 'type')->dropDownList(MenuItem::getTypes(), ['id' => 'type']) ?>

<?php
/** 
 * EN: Module List: Populated using Reflection to avoid heavy module booting and save performance.
 */
echo $form->field($model, 'module', ['options' => ['id' => 'module-list-div']])->dropDownList(MenuItem::getModuleList(), [
    'id' => 'module-list',
    'prompt' => Module::t('Select Module')
]);

/**
 * EN: Dependent Dropdowns: Initial state (disabled/enabled) is controlled by PHP to prevent UI flickering during Pjax..
 */
echo $form->field($model, 'routeType', ['options' => ['id' => 'routeType-list-div']])->dropDownList([], [
    'id' => 'routeType-list',
    'prompt' => Module::t('Select...'),
    'disabled' => empty($model->module)
]);

echo $form->field($model, 'route', ['options' => ['id' => 'route-list-div']])->dropDownList([], [
    'id' => 'route-list',
    'prompt' => Module::t('Select...'),
    'disabled' => empty($model->routeType)
]);

echo $form->field($model, 'model', ['options' => ['id' => 'model-list-div']])->dropDownList([], [
    'id' => 'model-list',
    'prompt' => Module::t('Select...'),
    'disabled' => empty($model->route)
]);

echo $form->field($model, 'url', ['options' => ['id' => 'url-input-div']])->textInput(['rows' => 6])->label(Module::t('URL'));

/**
 * EN: RBAC Access List: Select2 widget for high-performance role/permission selection following Portalium standards.
 */
echo $form->field($model, 'name_auth', ['options' => ['id' => 'name-auth-input-div']])->widget(Select2::classname(), [
    'data' => MenuItem::getAuthList(),
    'options' => [
        'id' => 'name-auth-input',
        'placeholder' => Module::t('Disabled'),
    ],
    'pluginOptions' => [
        'allowClear' => true,
        'width' => '100%'
    ]
]);
?>

<?php
/**
 * EN: Security & Styling: Injected via RegisterJs/Css to follow Portalium framework guidelines.
 */
$this->registerJs('
    // EN: CSRF Token Injection: Ensures all AJAX POST requests are authenticated by Portalium security layer.
    $(document).ajaxSend(function(event, jqxhr, settings) {
        if (settings.type == "POST") {
            settings.data = settings.data + "&' . Yii::$app->request->csrfParam . '=' . Yii::$app->request->csrfToken . '";
        }
    });
');

$this->registerCss(
    <<<CSS
    /*EN: Layout Fix: Enforces Select2 to respect container width within the Portalium theme. */
    #name-auth-input-div .selection {
        width: 100% !important;
    }
CSS
);
?>