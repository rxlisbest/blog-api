<?php

use yii\db\Migration;
use yii\db\Schema;

class m170815_124930_discuss extends Migration
{
    public function safeUp(){
	    $this->createTable('{{%discuss}}', [
		    'id' => Schema::TYPE_INTEGER . '(11) NOT NULL AUTO_INCREMENT',
		    'user_id' => Schema::TYPE_INTEGER . '(11) NOT NULL DEFAULT 0 COMMENT \'用户ID\'',
		    'content' => Schema::TYPE_TEXT . ' DEFAULT NULL COMMENT \'讨论正文\'',
		    'article_id' => Schema::TYPE_INTEGER . '(11) NOT NULL DEFAULT 0 COMMENT \'文章ID\'',
		    'status' => Schema::TYPE_INTEGER . '(3) NOT NULL DEFAULT 0 COMMENT \'讨论状态\'',
		    'time' => Schema::TYPE_INTEGER . '(11) NOT NULL DEFAULT 0 COMMENT \'视频播放时间\'',
		    'create_time' => Schema::TYPE_STRING . '(20) NOT NULL DEFAULT \'0\' COMMENT \'创建时间\'',
		    'update_time' => Schema::TYPE_STRING . '(20) NOT NULL DEFAULT \'0\' COMMENT \'编辑时间\'',
		    'PRIMARY KEY (id)',
	    ]);
    }

    public function safeDown(){
	    $this->dropTable('{{%discuss}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170815_124930_discuss cannot be reverted.\n";

        return false;
    }
    */
}
