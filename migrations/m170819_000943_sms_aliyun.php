<?php

use yii\db\Migration;
use yii\db\Schema;

class m170819_000943_sms_aliyun extends Migration
{
	public function safeUp(){
		$this->createTable('{{%sms_aliyun}}', [
			'id' => Schema::TYPE_INTEGER . '(11) NOT NULL AUTO_INCREMENT',
			'ParamString' => Schema::TYPE_STRING . '(255) NOT NULL DEFAULT 0 COMMENT \'code\'',
			'RecNum' => Schema::TYPE_STRING . '(255) NOT NULL DEFAULT 0 COMMENT \'code\'',
			'TemplateCode' => Schema::TYPE_STRING . '(255) NOT NULL DEFAULT 0 COMMENT \'code\'',
			'ip' => Schema::TYPE_STRING . '(20) NOT NULL DEFAULT \'0\' COMMENT \'IP\'',
			'create_time' => Schema::TYPE_BIGINT . '(20) NOT NULL DEFAULT \'0\' COMMENT \'创建时间\'',
			'update_time' => Schema::TYPE_BIGINT . '(20) NOT NULL DEFAULT \'0\' COMMENT \'编辑时间\'',
			'PRIMARY KEY (id)',
		]);
	}

	public function safeDown(){
		$this->dropTable('{{%sms_aliyun}}');
	}
}
