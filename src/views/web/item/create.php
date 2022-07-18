<?php

use portalium\menu\bundles\DropMenuAsset;
use yii\helpers\Html;
use portalium\menu\Module;
use yii\jui\Sortable;
use yii\jui\Droppable;
use yii\jui\Draggable;
/* @var $this yii\web\View */
/* @var $model portalium\menu\models\MenuItem */

$this->title = Module::t('Create Menu Item');
$this->params['breadcrumbs'][] = ['label' => Module::t('Menu Items'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
  #droppable { width: 150px; height: 150px; padding: 0.5em; float: left; margin: 10px; }
  #draggable, #draggable-nonvalid { width: 100px; height: 100px; padding: 0.5em; float: left; margin: 10px 10px 10px 0; }
  </style>
  
<div class="menu-item-create">
    <?php

    echo \portalium\menu\widgets\DropMenu::widget(
        [
            'model' => $model,
            'id_menu' => $id_menu,
        ]
    );
    
    ?>


</div>

