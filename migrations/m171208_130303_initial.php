<?php

use humhub\components\Migration;

class m171208_130303_initial extends Migration
{
    public function up()
    {
        $this->createTable('announcement', [
            'id' => 'pk',
            'message' => 'TEXT NULL',
            'closed'=> 'tinyint(4) DEFAULT 0'
        ], '');

        $this->createTable('announcement_user', [
            'id' => 'pk',
            'announcement_id' => 'int(11) NOT NULL',
            'user_id' => 'int(11) NOT NULL',
            'confirmed' => 'tinyint(4) NULL',
        ], '');

        $this->createIndex('unique_message_user', 'announcement_user', 'announcement_id,user_id', true);
    }

    public function down()
    {
        echo "m171208_130303_initial cannot be reverted.\n";

        return false;
    }

}
