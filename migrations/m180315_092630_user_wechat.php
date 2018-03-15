<?php

use yii\db\Migration;

/**
 * Class m180315_092630_user_wechat
 */
class m180315_092630_user_wechat extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp(){
        $this->dropPrimaryKey('user_id', 'user_wechat');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown(){
        $this->addPrimaryKey('user_id', 'user_wechat', 'user_id');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180315_092630_user_wechat cannot be reverted.\n";

        return false;
    }
    */
}
