<?php

use yii\db\Migration;

/**
 * Class m180315_143655_oauth2_user
 */
class m180315_143655_oauth2_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp(){
        $this->insert('oauth2_user', [
            'username' => 'admin',
            'cellphone' => '18363857076',
            'password' => 'b8f53bcaa56e3f1ba9c05fca090bd15b',
            'salt' => 'u94Y8a',
            'roles' => '',
            'scope' => '',
            'client_id' => md5('blog')
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown(){
        $this->delete('oauth2_user', ['username' => 'admin']);
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180315_143655_oauth2_user cannot be reverted.\n";

        return false;
    }
    */
}
