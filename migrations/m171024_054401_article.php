<?php

use yii\db\Migration;

class m171024_054401_article extends Migration{

    public function safeUp(){
	    $this->addColumn('article', 'sort', $this->integer() . ' DEFAULT 0 COMMENT "排序号"');
    }

    public function safeDown(){
	    $this->dropColumn('article', 'sort');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m171024_054401_article cannot be reverted.\n";

        return false;
    }
    */
}
