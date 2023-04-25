<?php

use portalium\db\Migration;
use portalium\menu\Module;
use portalium\user\Module as UserModule;

class m220218_222704_menu_item extends Migration
{



    public function safeUp()
    {
        $tableOptions = 'ENGINE=InnoDB';

        $this->createTable(
            '{{%' . Module::$tablePrefix . 'item}}',
            [
                'id_item' => $this->primaryKey(11),
                'label' => $this->string(255),
                'slug' => $this->string(255),
                'type' => $this->integer(11),
                'style' => $this->text()->defaultValue('{}'),
                'data' => $this->text()->defaultValue(0),
                'sort' => $this->integer(11)->notNull()->defaultValue(0),
                'id_menu' => $this->integer(11)->notNull(),
                'name_auth' => $this->string(64),
                'id_user' => $this->integer(11)->notNull(),
                'date_create' => $this->datetime()->notNull()->defaultExpression("CURRENT_TIMESTAMP"),
                'date_update' => $this->datetime()->notNull()->defaultExpression("CURRENT_TIMESTAMP"),
            ],
            $tableOptions
        );
        // creates index for column `id_user`
        $this->createIndex(
            '{{%idx-' . Module::$tablePrefix . 'item-id_user}}',
            '{{%' . Module::$tablePrefix . 'item}}',
            'id_user'
        );

        // add foreign key for table `{{%user}}`
        $this->addForeignKey(
            '{{%fk-' . Module::$tablePrefix . 'item-id_user}}',
            '{{%' . Module::$tablePrefix . 'item}}',
            'id_user',
            '{{%' . UserModule::$tablePrefix . 'user}}',
            'id_user',
            'RESTRICT'
        );

        // creates index for column `id_menu`
        $this->createIndex(
            '{{%idx-' . Module::$tablePrefix . 'item-id_menu}}',
            '{{%' . Module::$tablePrefix . 'item}}',
            'id_menu'
        );

        // add foreign key for table `{{%menu}}`
        $this->addForeignKey(
            '{{%fk-' . Module::$tablePrefix . 'item-id_menu}}',
            '{{%' . Module::$tablePrefix . 'item}}',
            'id_menu',
            '{{%' . Module::$tablePrefix . 'menu}}',
            'id_menu',
            'RESTRICT'
        );


    }

    public function safeDown()
    {
        $this->dropTable('{{%' . Module::$tablePrefix . 'item}}');
    }
}
