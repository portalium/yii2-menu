<?php

use yii\helpers\Html;
use portalium\menu\Module;
/* @var $this yii\web\View */
/* @var $model portalium\menu\models\Menu */

$this->title = Module::t('Create Menu');
$this->params['breadcrumbs'][] = ['label' => Module::t('Menu'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="menu-create">


    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
