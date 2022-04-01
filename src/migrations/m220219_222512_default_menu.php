<?php

use yii\db\Migration;
use portalium\site\models\Form;
use portalium\menu\models\Menu;
use portalium\menu\models\MenuItem;
use portalium\menu\Module;
use portalium\site\widgets\LoginButton;

class m220219_222512_default_menu extends Migration
{
    public function up()
    {
        //insert
        $this->insert('menu_menu', [
            'id_menu' => '1',
            'name' => 'Main Menu',
            'slug' => 'main-menu',
            'type' => Menu::TYPE['web']
        ]);

        $this->insert('menu_item', [
            'label' => 'Menu',
            'slug' => 'menu',
            'type' => MenuItem::TYPE['route'],
            'icon' => 'fa fa-users',
            'data' => JSON_encode([
                'type' => '1',
                'data' => [
                    'route' => '/menu',
                ]
            ]),
            'sort' => '2',
            'name_auth' => 'admin',
            'id_parent' => '0',
            'id_menu' => '1',
        ]);

    }

    public function down()
    {

    }
}