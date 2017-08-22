<?php

use yii\db\Migration;
use yii\db\Schema;

class m170806_004618_file extends Migration
{
    public function safeUp()
    {
	    $this->createTable('{{%file}}', [
		    'id' => Schema::TYPE_INTEGER . '(11) NOT NULL AUTO_INCREMENT',
		    'user_id' => Schema::TYPE_INTEGER . '(11) NOT NULL DEFAULT 0 COMMENT \'用户ID\'',
		    'hash' => Schema::TYPE_STRING . '(255) DEFAULT NULL COMMENT \'HASH\'',
		    'name' => Schema::TYPE_STRING . '(255) DEFAULT NULL COMMENT \'文件名称\'',
		    'type' => Schema::TYPE_STRING . '(20) DEFAULT NULL COMMENT \'文件类型\'',
		    'size' => Schema::TYPE_INTEGER . '(11) NOT NULL DEFAULT 0 COMMENT \'文件大小\'',
		    'domain' => Schema::TYPE_STRING . '(255) DEFAULT NULL COMMENT \'文件域名\'',
		    'save_name' => Schema::TYPE_STRING . '(255) DEFAULT NULL COMMENT \'保存文件名称\'',
		    'transcode_id' => Schema::TYPE_STRING . '(255) DEFAULT NULL COMMENT \'转码ID\'',
		    'transcode_type' => Schema::TYPE_STRING . '(20) DEFAULT NULL COMMENT \'转码类型\'',
		    'transcode_name' => Schema::TYPE_STRING . '(255) DEFAULT NULL COMMENT \'转码文件名称\'',
		    'is_transcoded' => Schema::TYPE_INTEGER . '(3) NOT NULL DEFAULT 0 COMMENT \'是否转码\'',
		    'create_time' => Schema::TYPE_BIGINT . '(20) NOT NULL DEFAULT \'0\' COMMENT \'创建时间\'',
		    'update_time' => Schema::TYPE_BIGINT . '(20) NOT NULL DEFAULT \'0\' COMMENT \'编辑时间\'',
		    'PRIMARY KEY (id)',
	    ]);
    }

    public function safeDown()
    {
	    $this->dropTable('{{%file}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170806_004618_file cannot be reverted.\n";

        return false;
    }
    */
}
