<?php

use yii\db\Schema;
use yii\db\Migration;

class m220218_222704_menu_item extends Migration
{

    public function init()
    {
        $this->db = 'db';
        parent::init();
    }

    public function safeUp()
    {
        $tableOptions = 'ENGINE=InnoDB';

        $this->createTable(
            '{{%menu_item}}',
            [
                'id_item'=> $this->primaryKey(11),
                'label'=> $this->string(255)->notNull(),
                'slug'=> $this->string(255)->notNull(),
                'type'=> $this->integer(11)->notNull(),
                'icon'=> $this->string(64)->notNull(),
                'data'=> $this->text()->notNull(),
                'sort'=> $this->integer(11)->notNull(),
                'id_parent'=> $this->integer(11)->notNull()->defaultValue(0),
                'id_menu'=> $this->integer(11)->notNull(),
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
