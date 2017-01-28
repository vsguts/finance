<?php

use yii\db\Migration;

class m170121_214232_user_status extends Migration
{
    public function up()
    {
        $this->renameColumn('user', 'status', 'status_old');
        $this->addColumn('user', 'status', "enum('active','disabled') NOT NULL DEFAULT 'active' AFTER email");
        $this->update('user', ['status' => 'disabled'], ['status_old' => 2]);
        $this->dropColumn('user', 'status_old');

        $this->update('lookup', ['code' => 'active', 'position' => 0], ['table' => 'user', 'field' => 'status', 'code' => 1]);
        $this->update('lookup', ['code' => 'disabled', 'position' => 0], ['table' => 'user', 'field' => 'status', 'code' => 2]);
    }

    public function down()
    {
        $this->update('lookup', ['code' => 1, 'position' => 10], ['table' => 'user', 'field' => 'status', 'code' => 'active']);
        $this->update('lookup', ['code' => 2, 'position' => 20], ['table' => 'user', 'field' => 'status', 'code' => 'disabled']);

        $this->addColumn('user', 'status_old', $this->integer()->notNull()->defaultValue(1)->after('password_reset_token'));
        $this->update('user', ['status_old' => 2], ['status' => 'disabled']);
        $this->dropColumn('user', 'status');
        $this->renameColumn('user', 'status_old', 'status');
    }
}
