<?php

use yii\db\Migration;
use yii\db\Schema;

class m170806_004603_article extends Migration
{
    public function safeUp()
    {
	    $this->createTable('{{%article}}', [
		    'id' => Schema::TYPE_INTEGER . '(11) NOT NULL AUTO_INCREMENT',
		    'user_id' => Schema::TYPE_INTEGER . '(11) NOT NULL DEFAULT 0 COMMENT \'用户ID\'',
		    'file_id' => Schema::TYPE_INTEGER . '(11) NOT NULL DEFAULT 0 COMMENT \'文件ID\'',
		    'title' => Schema::TYPE_STRING . '(255) DEFAULT NULL COMMENT \'文章标题\'',
		    'cover_src' => Schema::TYPE_STRING . '(255) DEFAULT NULL COMMENT \'封面链接\'',
		    'content' => Schema::TYPE_TEXT . ' DEFAULT NULL COMMENT \'文章正文\'',
		    'category_id' => Schema::TYPE_INTEGER . '(11) NOT NULL DEFAULT 0 COMMENT \'文章分类\'',
		    'status' => Schema::TYPE_INTEGER . '(3) NOT NULL DEFAULT 0 COMMENT \'文章状态\'',
		    'create_time' => Schema::TYPE_STRING . '(20) NOT NULL DEFAULT \'0\' COMMENT \'创建时间\'',
		    'update_time' => Schema::TYPE_STRING . '(20) NOT NULL DEFAULT \'0\' COMMENT \'编辑时间\'',
		    'PRIMARY KEY (id)',
	    ]);
    }

    public function safeDown()
    {
	    $this->dropTable('{{%article}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170806_004603_article cannot be reverted.\n";

        return false;
    }
    */
}
