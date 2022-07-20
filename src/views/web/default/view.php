<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use portalium\menu\Module;
use portalium\theme\widgets\Panel;
/* @var $this yii\web\View */
/* @var $model portalium\menu\models\Menu */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Module::t('Menu'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="menu-view">

    <?php Panel::begin([
        'title' => Module::t('Menu'),
        'actions' => [
            Html::a(Module::t('Update'), ['update', 'id' => $model->id_menu], ['class' => 'btn btn-primary']),
            Html::a(Module::t('Delete'), ['delete', 'id' => $model->id_menu], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => Module::t('Are you sure you want to delete this item?'),
                    'method' => 'post',
                ]
            ]),
        ],
    ]) ?>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id_menu',
            'name',
            'slug',
            'date_create',
            'date_update',
        ],
    ]) ?>
    <?php Panel::end() ?>

</div>