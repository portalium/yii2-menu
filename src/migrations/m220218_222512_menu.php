<?php

use yii\db\Schema;
use yii\db\Migration;
use portalium\menu\Module;

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
                'date_create'=> $this->datetime()->notNull()->defaultExpression("CURRENT_TIMESTAMP"),
                'date_update'=> $this->datetime()->notNull()->defaultExpression("CURRENT_TIMESTAMP"),
            ],$tableOptions
        );

    }

    public function safeDown()
    {
        $this->dropTable('{{%' . Module::$tablePrefix . 'menu}}');
    }
}
