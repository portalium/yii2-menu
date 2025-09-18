<?php

use portalium\db\Migration;
use portalium\menu\Module;
use portalium\user\Module as UserModule;

class m220218_222512_menu extends Migration
{

    public function safeUp()
    {
        $tableOptions = 'ENGINE=InnoDB';

        $this->createTable(
            '{{%' . Module::$tablePrefix . 'menu}}',
            [
                'id_menu'=> $this->primaryKey(11),
                'name'=> $this->string(255)->notNull(),
                'slug'=> $this->string(255)->notNull(),
                'type'=> $this->integer(11)->notNull(),
                'direction'=> $this->integer(11)->notNull()->defaultValue(1),
                'placement'=> $this->integer(11)->notNull()->defaultValue(1),
                'id_user' => $this->integer(11)->notNull(),
                'date_create'=> $this->datetime()->notNull()->defaultExpression("CURRENT_TIMESTAMP"),
                'date_update'=> $this->datetime()->notNull()->defaultExpression("CURRENT_TIMESTAMP"),
            ],$tableOptions
        );

        // creates index for column `id_user`
        $this->createIndex(
            '{{%idx-' . Module::$tablePrefix . 'menu-id_user}}',
            '{{%' . Module::$tablePrefix . 'menu}}',
            'id_user'
        );

        // add foreign key for table `{{%user}}`
        $this->addForeignKey(
            '{{%fk-' . Module::$tablePrefix . 'menu-id_user}}',
            '{{%' . Module::$tablePrefix . 'menu}}',
            'id_user',
            '{{%' . UserModule::$tablePrefix . 'user}}',
            'id_user',
            'RESTRICT'
        );

    }

    public function safeDown()
    {
        $this->dropTable('{{%' . Module::$tablePrefix . 'menu}}');
    }
}
