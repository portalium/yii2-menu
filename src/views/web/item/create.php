<?php

use portalium\menu\Module;
use portalium\menu\models\Menu;
/* @var $this yii\web\View */
/* @var $model portalium\menu\models\MenuItem */

$id_menu = Yii::$app->request->get('id_menu');
$this->title = Menu::findOne($id_menu)->name;
$this->params['breadcrumbs'][] = ['label' => Module::t('Menus'), 'url' => ['default/index']];
$this->params['breadcrumbs'][] = ['label' => Menu::findOne($id_menu)->name];
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


