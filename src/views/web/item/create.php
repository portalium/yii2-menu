<?php

use portalium\menu\Module;
/* @var $this yii\web\View */
/* @var $model portalium\menu\models\MenuItem */

$this->title = Module::t('Create Menu Item');
$this->params['breadcrumbs'][] = ['label' => Module::t('Menu Items'), 'url' => ['index', 'id_menu' => $id_menu]];
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
    #droppable { width: 150px; height: 150px; padding: 0.5em; float: left; margin: 10px; }
    #draggable, #draggable-nonvalid { width: 100px; height: 100px; padding: 0.5em; float: left; margin: 10px 10px 10px 0px; }
</style>

    <?php

    echo \portalium\menu\widgets\DropMenu::widget(
        [
            'model' => $model,
            'id_menu' => $id_menu,
            'menuModel' => $menuModel,
        ]
    );
    
    ?>


