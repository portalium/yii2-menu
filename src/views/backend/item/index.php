<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use portalium\menu\Module;

/* @var $this yii\web\View */
/* @var $searchModel portalium\menu\models\MenuItemSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
if (isset($name)) {
    $this->title = Module::t('{name} Items',
        ['name' => $name]);
} else {
    $this->title = Module::t('Menu Items');
}
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="menu-item-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Create Menu Item'), ['/menu/item/create', 'id_menu' => (isset($id_menu)) ? $id_menu : null], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id_item',
            'label',
            'slug',
            'url:ntext',
            'icon',
            //'id_parent',
            //'id_menu',
            //'date_create',
            //'date_update',
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
