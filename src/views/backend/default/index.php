<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use portalium\menu\Module;

/* @var $this yii\web\View */
/* @var $searchModel portalium\menu\models\MenuSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Menus');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="menu-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Create Menu'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id_menu',
            'name',
            'slug',
            'date_create',
            'date_update',
            ['class' => 'yii\grid\ActionColumn',
                'template' => "{view} {update} {delete} {items} ",
                'buttons' => [
                    'items' => function ($url, $model) {
                        return Html::a(Html::tag('i', '', ['class' => 'fa-thin fa-bars']), ['/menu/item/index', 'id_menu' => $model->id_menu], ['title' => Module::t('Items')]);
                    }
                ]]
        ],
    ]); ?>


</div>
