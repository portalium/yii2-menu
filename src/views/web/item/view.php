<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use portalium\menu\Module;
use portalium\theme\widgets\Panel;
/* @var $this yii\web\View */
/* @var $model portalium\menu\models\MenuItem */

$this->title = $model->id_item;
$this->params['breadcrumbs'][] = ['label' => Module::t('Menu Items'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="menu-item-view">

    <?php Panel::begin([
        'title' => Module::t('Job Application'),
        'actions' => [
            Html::a(Module::t('Update'), ['update', 'id' => $model->id_item], ['class' => 'fa fa-pencil btn btn-primary']),
            Html::a(Module::t('Delete'), ['delete', 'id' => $model->id_item], [
                'class' => 'fa fa-trash btn btn-danger',
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
            'id_item',
            'label',
            'slug',
            'icon',
            'id_menu',
            'date_create',
            'date_update',
        ],
    ]) ?>

</div>
