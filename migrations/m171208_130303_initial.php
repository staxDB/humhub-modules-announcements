<?php

use humhub\components\Migration;

class m171208_130303_initial extends Migration
{
    public function up()
    {
        $this->createTable('announcement', array(
            'id' => 'pk',
            'title' => 'varchar(255) NOT NULL',
            'message' => 'TEXT NULL',
        ), '');

        $this->createTable('announcement_user', array(
            'id' => 'pk',
            'announcement_id' => 'int(11) NOT NULL',
            'user_id' => 'int(11) NOT NULL',
            'confirmed' => 'tinyint(4) NULL',
        ), '');

        $this->createIndex('unique_message_user', 'announcement_user', 'announcement_id,user_id', true);
//        $this->addForeignKey('fk-message-user', 'announcement_user', 'announcement_id', 'announcement', 'id', 'CASCADE','CASCADE');
    }

    public function down()
    {
        echo "m171208_130303_initial cannot be reverted.\n";

        return false;
    }

    /*
      // Use safeUp/safeDown to do migration with transaction
      public function safeUp()
      {
      }

      public function safeDown()
      {
      }
     */
}
