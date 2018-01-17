<?php

use yii\db\Migration;

class m160428_084227_widgets_menu extends Migration
{
    public function safeUp()
    {

        $tableOptions = null;

        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%zoo_menu}}', [
            'id' => $this->primaryKey(),   
            'type' => $this->string(),         
            'name' => $this->string()->notNull(),
            'menu' => $this->string(),
            'params'=> $this->text(),
            'parent_id' => $this->integer()->defaultValue(0),
            'application_id' => $this->integer()->defaultValue(0),
            'category_id' => $this->integer()->defaultValue(0),
            'item_id' => $this->integer()->defaultValue(0),
            'sort' => $this->integer(),
            'url'=> $this->text(),
            'updated_at' => $this->integer(),
        ], $tableOptions);
                
    }

    public function safeDown()
    {
        $this->dropTable('{{%zoo_menu}}');
    }

}
