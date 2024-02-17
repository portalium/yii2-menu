<?php

use yii\helpers\Html;
use portalium\menu\Module;
use portalium\theme\widgets\Panel;
use portalium\theme\widgets\GridView;
use portalium\theme\widgets\ActionColumn;
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

    <?php Panel::begin([
        'title' => Module::t('Items'),
        'actions' => [
            Html::a(Module::t(''), ['/menu/item/create', 'id_menu' => (isset($id_menu)) ? $id_menu : null], ['class' => 'fa fa-plus btn btn-success']),
        ],
    ]) ?>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id_item',
            'label',
            'slug',
            'icon',
            //'id_menu',
            //'date_create',
            //'date_update',
            ['class' => ActionColumn::class],
        ],
        'layout' => '{items}{summary}{pagesizer}{pager}',
    ]); ?>
    
    <?php Panel::end() ?>

</div>
