<?php

use portalium\menu\models\Menu;
use portalium\menu\models\MenuItem;
use portalium\db\Migration;

class m010101_010102_menu_menu extends Migration
{

    public function up()
    {

        $this->insert('menu_menu', [
            'id_menu' => '1',
            'name' => 'Web Main Menu',
            'slug' => 'web-main-menu',
            'type' => Menu::TYPE['web'],
            'direction' => Menu::DIRECTION['vertical'], 
            'id_user' => '1',
        ]);

        $this->insert('menu_menu', [
            'id_menu' => '2',
            'name' => 'Web Side Menu',
            'slug' => 'web-side-menu',
            'type' => Menu::TYPE['web'],
            'direction' => Menu::DIRECTION['vertical'],
            'id_user' => '1',
        ]);

        $id_menu = Menu::find()->where(['slug' => 'web-main-menu'])->one()->id_menu;

        $id_item = MenuItem::find()->where(['slug' => 'site'])->one();

        if(!$id_item){
            $this->insert('menu_item', [
                'id_item' => NULL,
                'label' => 'Site',
                'slug' => 'site',
                'type' => '3',
                'style' => '{"icon":"fa-cog","color":"","iconSize":"","display":"3","childDisplay":"3"}',
                'data' => '{"data":{"url":"#"}}',
                'sort' => '1',
                'id_menu' => $id_menu,
                'name_auth' => 'admin',
                'id_user' => '1',
                'date_create' => '2022-06-13 15:32:26',
                'date_update' => '2022-06-13 15:32:26',
            ]);
        }else {
            $id_item = MenuItem::find()->where(['slug' => 'site'])->one()->id_item;
        }

        $id_item = MenuItem::find()->where(['slug' => 'site'])->one()->id_item;

        $this->batchInsert('menu_item', ['id_item', 'label', 'slug', 'type', 'style', 'data', 'sort', 'id_menu', 'name_auth', 'id_user', 'date_create', 'date_update'], [
            [null, 'Menü', 'menu-parent', '2', '{"icon":"","color":"","iconSize":"","display":"","childDisplay":false}', '{"data":{"module":"menu","routeType":"action","route":"\\/menu\\/default\\/index","model":"","menuRoute":null,"menuType":"web"}}', '3', $id_menu, 'menuWebDefaultIndex', 1, '2022-06-13 15:28:07', '2022-06-13 15:28:07'],
        ]);

        $ids = $this->db->createCommand('SELECT id_item FROM menu_item WHERE slug in ("menu-parent")')->queryColumn();


        foreach ($ids as $id) {
            $this->insert('menu_item_child', [
                'id_item' => $id_item,
                'id_child' => $id
            ]);
        }

    }

    public function down()
    {
        $this->dropTable('site_setting');
    }
}
