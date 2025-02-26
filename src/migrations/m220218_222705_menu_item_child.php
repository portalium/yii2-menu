<?php

use portalium\db\Migration;
use portalium\menu\Module;

class m220218_222705_menu_item_child extends Migration
{
    public function safeUp()
    {
        $tableOptions = 'ENGINE=InnoDB';

        $this->createTable(
            '{{%' . Module::$tablePrefix . 'item_child}}',
        [
            'id_item'  => $this->integer(11)->notNull(),
            'id_child' => $this->integer(11)->notNull(),
        ],
        $tableOptions);

        $this->addPrimaryKey(
            '{{%pk-' . Module::$tablePrefix . 'item_child}}',
            '{{%' . Module::$tablePrefix . 'item_child}}',
            ['id_item', 'id_child']
        );
        $this->createIndex(
            '{{%idx-' . Module::$tablePrefix . 'item_child-id_item}}',
            '{{%' . Module::$tablePrefix . 'item_child}}',
            'id_item'
        );
        $this->createIndex(
            '{{%idx-' . Module::$tablePrefix . 'item_child-id_child}}',
            '{{%' . Module::$tablePrefix . 'item_child}}',
            'id_child'
        );

        $this->addForeignKey(
            '{{%fk-' . Module::$tablePrefix . 'item_child-id_item}}',
            '{{%' . Module::$tablePrefix . 'item_child}}',
            'id_item',
            '{{%' . Module::$tablePrefix . 'item}}',
            'id_item',
            'RESTRICT'
        );

        $this->addForeignKey(
            '{{%fk-' . Module::$tablePrefix . 'item_child-id_child}}',
            '{{%' . Module::$tablePrefix . 'item_child}}',
            'id_child',
            '{{%' . Module::$tablePrefix . 'item}}',
            'id_item',
            'RESTRICT'
        );
    }

    public function safeDown()
    {
        $this->dropTable('{{%' . Module::$tablePrefix . 'item_child}}');
    }
}
