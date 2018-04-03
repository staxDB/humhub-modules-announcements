<?php

use yii\db\Migration;

class m180306_133103_add_foreignkeys extends Migration
{
    public function safeUp()
    {
        try {
            $this->addForeignKey('fk_announcement_user', 'announcement_user', 'announcement_id', 'announcement', 'id', 'CASCADE','CASCADE');
        } catch(Exception $e) {
            Yii::error($e);
        }
    }

    public function safeDown()
    {
        try {
            $this->dropForeignKey('fk_announcement_user', 'announcement_user');
        } catch(Exception $e) {
            Yii::error($e);
        }
//        echo "m170830_122437_foreignkeys.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180306_133103_add_foreignkeys cannot be reverted.\n";

        return false;
    }
    */
}
