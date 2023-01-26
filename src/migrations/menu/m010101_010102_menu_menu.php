<?php

use portalium\menu\models\Menu;
use portalium\menu\models\MenuItem;
use yii\db\Migration;
use portalium\site\models\Form;

class m010101_010102_menu_menu extends Migration
{

    public function up()
    {

        $this->insert('menu_menu', [
            'id_menu' => '1',
            'name' => 'Web Menu',
            'slug' => 'web-menu',
            'type' => Menu::TYPE['web'],
            'id_user' => '1',
        ]);
        $id_menu = Menu::find()->where(['slug' => 'web-menu'])->one()->id_menu;

        $this->batchInsert('menu_item', ['id_item', 'label', 'slug', 'type', 'style', 'data', 'sort', 'id_menu', 'name_auth', 'id_user', 'date_create', 'date_update'], [
            [null, 'Menü', 'menu-parent', '2', '{"icon":"","color":"","iconSize":""}', '{"type":"2","data":{"module":"menu","routeType":"action","route":"\\/menu\\/default\\/index","model":null,"menuRoute":null,"menuType":"web"}}', '1', $id_menu, 'menuWebDefaultIndex', 1, '2022-06-13 15:28:07', '2022-06-13 15:28:07'],
        ]);

    }

    public function down()
    {
        $this->dropTable('site_setting');
    }
}
