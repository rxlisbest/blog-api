<?php

use yii\db\Migration;

/**
 * Class m180315_143648_oauth2_client
 */
class m180315_143648_oauth2_client extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp(){
        $this->insert('oauth2_client', [
            'client_id' => '5ab1eafb971da',
            'client_secret' => 'xJ5Qvz6l',
            'redirect_uri' => '/',
            'grant_type' => 'password',
            'scope' => 'all',
            'created_at' => time(),
            'updated_at' => time(),
            'created_by' => time(),
            'updated_by' => time(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown(){
        $this->delete('oauth2_client', ['client_id' => '5ab1eafb971da']);
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180315_143648_oauth2_client cannot be reverted.\n";

        return false;
    }
    */
}
