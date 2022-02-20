<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model portalium\menu\models\MenuItem */

$this->title = Yii::t('app', 'Update Menu Item: {name}', [
    'name' => $model->id_item,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Menu Items'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id_item, 'url' => ['view', 'id_item' => $model->id_item]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="menu-item-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
