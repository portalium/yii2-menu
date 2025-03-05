<?php

use yii\helpers\Url;
use yii\helpers\Html;
use portalium\menu\Module;
use portalium\theme\widgets\Panel;
use portalium\theme\widgets\GridView;
use portalium\theme\widgets\ActionColumn;
/* @var $this yii\web\View */
/* @var $searchModel portalium\menu\models\MenuSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Module::t('Menus');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="menu-index">

    <?php Panel::begin([
        'title' => Module::t('Menus'),
        'actions' => [
            Html::a('', ['create'], ['class' => 'fa fa-plus btn btn-success', 'title' => Module::t('Create Menu')]),
        ],
    ]) ?>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'portalium\grid\SerialColumn'],

            //'id_menu',
            'name',
            'slug',
            'date_create',
            'date_update',
            ['class' => ActionColumn::class, 'header' => Module::t('Actions'),
                'template' => "{view} {update} {delete} {items} ",
                'buttons' => [
                    'items' => function ($url, $model) {
                        return Html::a(
                            Html::tag('i', '', ['class' => 'fa fa-thin fa-bars']),
                            Url::toRoute(['/menu/item/index', 'id_menu' => $model->id_menu], ['title' => Module::t('Items')]),
                            ['class' => 'btn btn-primary btn-xs', 'style' => 'padding: 2px 9px 2px 9px; display: inline-block;', 'title' => Module::t('Edit')]
                        );
                    }
                ]]
        ],
        'layout' => '{items}{summary}{pagesizer}{pager}',
    ]); ?>

    <?php Panel::end() ?>
</div>
