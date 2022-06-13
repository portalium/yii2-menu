<?php

use yii\helpers\Html;
use portalium\menu\Module;
/* @var $this yii\web\View */
/* @var $model portalium\menu\models\MenuItem */

$this->title = Module::t('Update Menu Item: {name}', [
    'name' => $model->id_item,
]);
$this->params['breadcrumbs'][] = ['label' => Module::t('Menu Items'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id_item, 'url' => ['view', 'id_item' => $model->id_item]];
$this->params['breadcrumbs'][] = Module::t('Update');
?>
<div class="menu-item-update">

    <?= $this->render('_form', [
        'model' => $model,
        'id_menu' => $id_menu,
    ]) ?>

</div>
