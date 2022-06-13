<?php

use yii\helpers\Html;
use portalium\menu\Module;
/* @var $this yii\web\View */
/* @var $model portalium\menu\models\MenuItem */

$this->title = Module::t('Create Menu Item');
$this->params['breadcrumbs'][] = ['label' => Module::t('Menu Items'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="menu-item-create">

    <?= $this->render('_form', [
        'model' => $model,
        'id_menu' => $id_menu,
    ]) ?>

</div>
