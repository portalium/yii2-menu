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
                'id_item' => $this->integer(11)->notNull(),
                'id_child' => $this->integer(11)->notNull(),
            ],
            $tableOptions
        );

        // creates index for column `id_item`
        $this->createIndex(
            '{{%idx-' . Module::$tablePrefix . 'item_child-id_item}}',
            '{{%' . Module::$tablePrefix . 'item_child}}',
            'id_item'
        );

        // add foreign key for table `{{%item}}`
        $this->addForeignKey(
            '{{%fk-' . Module::$tablePrefix . 'item_child-id_item}}',
            '{{%' . Module::$tablePrefix . 'item_child}}',
            'id_item',
            '{{%' . Module::$tablePrefix . 'item}}',
            'id_item',
            'RESTRICT'
        );

        // creates index for column `id_child`
        $this->createIndex(
            '{{%idx-' . Module::$tablePrefix . 'item_child-id_child}}',
            '{{%' . Module::$tablePrefix . 'item_child}}',
            'id_child'
        );

        // add foreign key for table `{{%item}}`
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
