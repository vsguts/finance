<?php

use yii\db\Migration;

class m170126_131835_languages extends Migration
{
    public function up()
    {
        $this->createTable('language', [
            'id'         => $this->primaryKey(),
            'code'       => $this->string()->notNull(),
            'short_name' => $this->string()->notNull(),
            'name'       => $this->string()->notNull(),
        ], $this->getTableOptions());
        $this->insert('language', ['code'=>'en-US', 'name'=>'English', 'short_name'=>'EN']);
        $this->insert('language', ['code'=>'ru-RU', 'name'=>'Русский', 'short_name'=>'RU']);

    }

    public function down()
    {
        $this->dropTable('language');
    }

    protected function getTableOptions()
    {
        if ($this->db->driverName === 'mysql') {
            return 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
    }

}
