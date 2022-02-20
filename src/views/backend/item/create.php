<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model portalium\menu\models\MenuItem */

$this->title = Yii::t('app', 'Create Menu Item');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Menu Items'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="menu-item-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
