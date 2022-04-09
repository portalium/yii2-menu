<?php

use yii\db\Schema;
use yii\db\Migration;

class m220218_222704_menu_item extends Migration
{



    public function safeUp()
    {
        $tableOptions = 'ENGINE=InnoDB';

        $this->createTable(
            '{{%menu_item}}',
            [
                'id_item'=> $this->primaryKey(11),
                'label'=> $this->string(255),
                'slug'=> $this->string(255),
                'type'=> $this->integer(11),
                'icon'=> $this->string(64),
                'data'=> $this->text(),
                'sort'=> $this->integer(11)->notNull(),
                'id_parent'=> $this->integer(11)->notNull()->defaultValue(0),
                'id_menu'=> $this->integer(11)->notNull(),
                'name_auth'=> $this->string(64),
                'date_create'=> $this->datetime()->notNull()->defaultExpression("CURRENT_TIMESTAMP"),
                'date_update'=> $this->datetime()->notNull()->defaultExpression("CURRENT_TIMESTAMP"),
            ],$tableOptions
        );

    }

    public function safeDown()
    {
        $this->dropTable('{{%menu_item}}');
    }
}
