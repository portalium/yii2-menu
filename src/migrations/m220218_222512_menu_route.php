<?php

use yii\db\Schema;
use yii\db\Migration;

class m220218_222512_menu_route extends Migration
{

    public function safeUp()
    {
        $tableOptions = 'ENGINE=InnoDB';

        $this->createTable(
            '{{%menu_menu_route}}',
            [
                'id_menu_route'=> $this->primaryKey(11),
                'title'=> $this->string(255)->notNull(),
                'route'=> $this->string(255)->notNull(),
                'type'=> $this->integer(11)->notNull(),
                'module'=> $this->string(255)->notNull(),
                'date_create'=> $this->datetime()->notNull()->defaultExpression("CURRENT_TIMESTAMP"),
                'date_update'=> $this->datetime()->notNull()->defaultExpression("CURRENT_TIMESTAMP"),
            ],$tableOptions
        );

    }

    public function safeDown()
    {
        $this->dropTable('{{%menu_menu_route}}');
    }
}
