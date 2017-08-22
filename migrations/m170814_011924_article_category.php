<?php

use yii\db\Migration;
use yii\db\Schema;

class m170814_011924_article_category extends Migration
{
    public function safeUp()
    {
	    $this->createTable('{{%article_category}}', [
		    'id' => Schema::TYPE_INTEGER . '(11) NOT NULL AUTO_INCREMENT',
		    'user_id' => Schema::TYPE_INTEGER . '(11) NOT NULL DEFAULT 0 COMMENT \'用户ID\'',
		    'parent_id' => Schema::TYPE_INTEGER . '(11) NOT NULL DEFAULT 0 COMMENT \'上级ID\'',
		    'title' => Schema::TYPE_STRING . '(255) DEFAULT NULL COMMENT \'分类标题\'',
		    'type' => Schema::TYPE_INTEGER . '(3) NOT NULL DEFAULT 0 COMMENT \'分类类型\'',
		    'status' => Schema::TYPE_INTEGER . '(3) NOT NULL DEFAULT 0 COMMENT \'分类状态\'',
		    'create_time' => Schema::TYPE_BIGINT . '(20) NOT NULL DEFAULT \'0\' COMMENT \'创建时间\'',
		    'update_time' => Schema::TYPE_BIGINT . '(20) NOT NULL DEFAULT \'0\' COMMENT \'编辑时间\'',
		    'PRIMARY KEY (id)',
	    ]);
    }

    public function safeDown()
    {
	    $this->dropTable('{{%article_category}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170814_011924_article_category cannot be reverted.\n";

        return false;
    }
    */
}
