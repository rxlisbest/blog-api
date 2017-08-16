<?php

use yii\db\Migration;
use yii\db\Schema;

class m170816_010423_user_wechat extends Migration
{
    public function safeUp(){
	    $this->createTable('{{%user_wechat}}', [
		    'user_id' => Schema::TYPE_INTEGER . '(11) NOT NULL DEFAULT 0 COMMENT \'用户ID\'',
		    'openid' => Schema::TYPE_STRING . '(255) NOT NULL DEFAULT 0 COMMENT \'openid\'',
		    'create_time' => Schema::TYPE_STRING . '(20) NOT NULL DEFAULT \'0\' COMMENT \'创建时间\'',
		    'update_time' => Schema::TYPE_STRING . '(20) NOT NULL DEFAULT \'0\' COMMENT \'编辑时间\'',
		    'PRIMARY KEY (user_id)',
	    ]);
    }

    public function safeDown(){
	    $this->dropTable('{{%user_wechat}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170816_010423_user_wechat cannot be reverted.\n";

        return false;
    }
    */
}
