<?php

use Yii;
use yii\helpers\Url;
use portalium\menu\Module;
use yii\web\NotFoundHttpException;
use portalium\base\Exception;
use kartik\depdrop\DepDrop;
use portalium\menu\models\MenuItem;

?>

<?= $form->field($model, 'label')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'slug')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'id_parent')->dropDownList(MenuItem::getParents($id_menu), ['id' => 'id_item'])->label(Module::t('Parent')) ?>

    
    <?= $form->field($model, 'type')->dropDownList(MenuItem::getTypes(), ['id' => 'type']) ?>
    <?php

    echo $form->field($model, 'module', ['options' => ['id' => 'module-list-div']])->dropDownList(MenuItem::getModuleList(), ['id' => 'module-list', 'prompt' => 'Select Module']);

    echo $form->field($model, 'routeType', ['options' => ['id' => 'routeType-list-div']])->dropDownList(MenuItem::getModuleList(), ['id' => 'module-list', 'prompt' => 'Select Module'])->widget(DepDrop::classname(), [
        'options' => ['id' => 'routeType-list'],
        'pluginOptions' => [
            'depends' => ['module-list'],
            'placeholder' => Module::t('Select...'),
            'url' => Url::to(['/menu/item/route-type']),
            'paramsBase' => [
                Yii::$app->request->csrfParam => Yii::$app->request->csrfToken,
            ]
        ]
    ]);

    echo $form->field($model, 'route', ['options' => ['id' => 'route-list-div']])->widget(DepDrop::classname(), [
        'options' => ['id' => 'route-list'],
        'pluginOptions' => [
            'depends' => ['module-list', 'routeType-list'],
            'placeholder' => Module::t('Select...'),
            'url' => Url::to(['/menu/item/route']),
            'paramsBase' => [
                Yii::$app->request->csrfParam => Yii::$app->request->csrfToken,
            ]
        ]
    ]);

    echo $form->field($model, 'model', ['options' => ['id' => 'model-list-div']])->widget(DepDrop::classname(), [
        'options' => ['id' => 'model-list'],
        'pluginOptions' => [
            'depends' => ['module-list', 'routeType-list', 'route-list',],
            'placeholder' => Module::t('Select...'),
            'url' => Url::to(['/menu/item/model']),
            'paramsBase' => [
                Yii::$app->request->csrfParam => Yii::$app->request->csrfToken,
            ]
        ],
        'pluginEvents' => [
            "depdrop:change" => "function(event, id, value) {
                }"
        ]
    ]);

    echo $form->field($model, 'url', ['options' => ['id' => 'url-input-div']])->textInput(['rows' => 6])->label(Module::t('URL'));


    echo $form->field($model, 'name_auth', ['options' => ['id' => 'name-auth-input-div']])->dropDownList(MenuItem::getAuthList(), ['id' => 'name-auth-input', 'prompt' => Module::t('Disabled'), "options" => ['role' => ['disabled' => true], 'permission' => ['disabled' => true]]]);

    ?>

<?php
$this->registerJs('
    $(document).ajaxSend(function(event, jqxhr, settings) {
        if (settings.type == "POST") {
            settings.data = settings.data + "&' . Yii::$app->request->csrfParam . '=' . Yii::$app->request->csrfToken . '";
        }
    });
');
?>