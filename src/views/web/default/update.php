<?php

use yii\helpers\Html;
use portalium\menu\Module;
/* @var $this yii\web\View */
/* @var $model portalium\menu\models\Menu */

$this->title = Module::t('Update Menu: {name}', [
    'name' => $model->name,
]);
$this->params['breadcrumbs'][] = ['label' => Module::t('Menus'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id_menu' => $model->id_menu]];
$this->params['breadcrumbs'][] = Module::t('Update');
?>
<div class="menu-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>