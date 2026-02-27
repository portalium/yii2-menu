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
 * @var yii\web\View $this
 * @var portalium\menu\models\MenuItem $model
 * @var yii\widgets\ActiveForm $form
 * @var int $id_menu
 * * Registering the asset bundle for cascade dropdown logic (depdropMenu.js).
 */
MenuDepDropAsset::register($this);
?>

<?= $form->field($model, 'label')->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'slug')->textInput(['maxlength' => true]) ?>

<?php 
/**
 * Parent List Optimization:
 * Uses Eager Loading behind the scenes to prevent N+1 queries during menu tree rendering.
 */
?>
<?= $form->field($model, 'id_parent')->dropDownList(MenuItem::getParents($id_menu), ['id' => 'id_item'])->label(Module::t('Parent')) ?>

<?= $form->field($model, 'type')->dropDownList(MenuItem::getTypes(), ['id' => 'type']) ?>

<?php
/** * Module List Optimization:
 * Populated using Reflection to avoid heavy module booting and save system resources.
 */
echo $form->field($model, 'module', ['options' => ['id' => 'module-list-div']])->dropDownList(MenuItem::getModuleList(), [
    'id' => 'module-list',
    'prompt' => Module::t('Select Module')
]);

/**
 * Dependent Dropdowns Logic:
 * Initial state (disabled/enabled) is controlled by PHP to prevent UI flickering during Pjax transitions.
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
 * RBAC Access Selection:
 * Select2 widget implemented for high-performance role/permission selection following Portalium standards.
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
 * Security & Styling Configuration:
 * Injected via RegisterJs/Css to ensure full compatibility with Portalium's dynamic asset loading.
 */
$this->registerJs('
    // CSRF Token Injection: Ensures all AJAX POST requests are authenticated by the Portalium security layer.
    $(document).ajaxSend(function(event, jqxhr, settings) {
        if (settings.type == "POST") {
            settings.data = settings.data + "&' . Yii::$app->request->csrfParam . '=' . Yii::$app->request->csrfToken . '";
        }
    });
');

$this->registerCss(
    <<<CSS
    /* Layout Fix: Enforces Select2 to respect container width within the Portalium responsive theme. */
    #name-auth-input-div .selection {
        width: 100% !important;
    }
CSS
);
?>