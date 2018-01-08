<?php

use yii\db\Migration;
use yii\db\Schema;

class m160909_202540_base extends Migration
{
    public function up()
    {
        $this->createTable('lookup', [
            'id'       => $this->primaryKey(),
            'table'    => $this->string(32)->notNull(),
            'field'    => $this->string(32)->notNull(),
            'code'     => $this->string(64)->notNull(),
            'position' => $this->integer()->notNull()->defaultValue(0),
            'name'     => $this->string()->notNull(),
            'UNIQUE KEY `code`(`table`, `field`, `code`)',
        ], $this->getTableOptions());

        $this->insert('lookup', ['table'=>'user', 'field'=>'status', 'position'=>10, 'code'=>1, 'name'=>'Active']);
        $this->insert('lookup', ['table'=>'user', 'field'=>'status', 'position'=>20, 'code'=>2, 'name'=>'Disabled']);
        $this->insert('lookup', ['table'=>'setting', 'field'=>'mailSendMethod', 'position'=>10, 'code'=>'php_mail', 'name'=>'via PHP mail function']);
        $this->insert('lookup', ['table'=>'setting', 'field'=>'mailSendMethod', 'position'=>20, 'code'=>'smtp', 'name'=>'via SMTP server']);
        $this->insert('lookup', ['table'=>'setting', 'field'=>'mailSendMethod', 'position'=>30, 'code'=>'file', 'name'=>'save to local EML files']);
        $this->insert('lookup', ['table'=>'setting', 'field'=>'smtpEncrypt', 'position'=>10, 'code'=>'none', 'name'=>'Disabled']);
        $this->insert('lookup', ['table'=>'setting', 'field'=>'smtpEncrypt', 'position'=>20, 'code'=>'tls', 'name'=>'TLS']);
        $this->insert('lookup', ['table'=>'setting', 'field'=>'smtpEncrypt', 'position'=>30, 'code'=>'ssl', 'name'=>'SSL']);
        $this->insert('lookup', ['table'=>'classification', 'field'=>'type', 'position'=>10, 'code'=>'inflow', 'name'=>'Inflow']);
        $this->insert('lookup', ['table'=>'classification', 'field'=>'type', 'position'=>20, 'code'=>'outflow', 'name'=>'Outflow']);
        $this->insert('lookup', ['table'=>'classification', 'field'=>'type', 'position'=>30, 'code'=>'transfer', 'name'=>'Transfer']);
        $this->insert('lookup', ['table'=>'classification', 'field'=>'type', 'position'=>40, 'code'=>'conversion', 'name'=>'Currency conversion']);
        $this->insert('lookup', ['table'=>'account', 'field'=>'status', 'code'=>'active', 'name'=>'Active']);
        $this->insert('lookup', ['table'=>'account', 'field'=>'status', 'code'=>'disabled', 'name'=>'Disabled']);
        $this->insert('lookup', ['table'=>'account', 'field'=>'import_processor', 'code'=>'custom', 'name'=>'Custom']);
        $this->insert('lookup', ['table'=>'account', 'field'=>'import_processor', 'code'=>'inexfinance', 'name'=>'Inexfinance Dirty']);


        $this->createTable('setting', [
            'name' => Schema::TYPE_STRING . '(128) NOT NULL PRIMARY KEY',
            'value' => $this->text(),
        ], $this->getTableOptions());

        $this->insert('setting', ['name' => 'adminEmail', 'value' => 'admin@example.com']);
        $this->insert('setting', ['name' => 'supportEmail', 'value' => 'admin@example.com']);
        $this->insert('setting', ['name' => 'applicationName', 'value' => 'Finance']);
        $this->insert('setting', ['name' => 'baseUrl', 'value' => 'http://finance.vsguts.ru/']);
        $this->insert('setting', ['name' => 'companyName', 'value' => 'Gvs']);
        $this->insert('setting', ['name' => 'poweredBy', 'value' => 'Yii2']);
        $this->insert('setting', ['name' => 'about', 'value' => 'Our system allows you to manage money transactions.']);
        
        $this->insert('setting', ['name' => 'mailSendMethod', 'value' => 'file']);


        $this->createTable('language', [
            'id'         => $this->primaryKey(),
            'code'       => $this->string()->notNull(),
            'short_name' => $this->string()->notNull(),
            'name'       => $this->string()->notNull(),
        ], $this->getTableOptions());
        $this->insert('language', ['code'=>'en-US', 'name'=>'English', 'short_name'=>'EN']);
        $this->insert('language', ['code'=>'ru-RU', 'name'=>'Русский', 'short_name'=>'RU']);


        $this->createTable('user', [
            'id'                   => $this->primaryKey(),
            'name'                 => $this->string()->notNull(),
            'email'                => $this->string()->notNull(),
            'status'               => "enum('active','disabled') NOT NULL DEFAULT 'active'",
            'auth_key'             => $this->string(32)->notNull(),
            'password_hash'        => $this->string()->notNull(),
            'password_reset_token' => $this->string(),
            'created_at'           => $this->integer()->notNull(),
            'updated_at'           => $this->integer()->notNull(),
        ], $this->getTableOptions());

        $this->insert('user', [ // Auth: root@example.com/root
            'id' => 1,
            'email' => 'root@example.com',
            'name' => 'Root Admin',
            'auth_key' => 'JxTq8CyzZwAa85PYUVy1GuI0X3WmUWUW',
            'password_hash' => '$2y$13$CPWVAx9rW6IYpVD7dU.mNe/mUWty8WN6Dheo0IrRkVAvubamuPqxK',
            'status' => 1,
        ]);


        $this->createTable('attachment', [
            'id'          => $this->primaryKey(),
            'table'       => $this->string()->notNull(),
            'object_id'   => $this->integer()->notNull(),
            'object_type' => $this->string(32)->notNull()->defaultValue('main'),
            'filename'    => $this->string()->notNull(),
            'filesize'    => $this->integer()->notNull(),
        ], $this->getTableOptions());


        $this->createTable('form_template', [
            'id' => $this->primaryKey(),
            'model' => $this->string(64)->notNull(),
            'template' => $this->string(128)->notNull(),
            'data' => $this->text(),
            'UNIQUE model_template(model, template)',
        ], $this->getTableOptions());


        $this->createTable('currency', [
            'id' => $this->primaryKey(),
            'name' => $this->string(128)->notNull(),
            'code' => $this->string(32)->notNull(),
            'symbol' => $this->string(64),
        ], $this->getTableOptions());

        // Demo data
        $this->insert('currency', ['code'=>'USD', 'name'=>'United States dollar', 'symbol' => '$']);
        $this->insert('currency', ['code'=>'EUR', 'name'=>'Euro', 'symbol' => '€']);
        $this->insert('currency', ['code'=>'UAH', 'name'=>'Ukrainian hryvnia', 'symbol' => '₴']);
        $this->insert('currency', ['code'=>'RUB', 'name'=>'Russian rouble', 'symbol' => '₽']);


        $this->createTable('currency_rate', [
            'id' => $this->primaryKey(),
            'currency_id' => $this->integer()->notNull(),
            'year' => $this->integer()->notNull(),
            'month' => $this->integer()->notNull(),
            'day' => $this->integer()->notNull(),
            'rate' => $this->decimal(12, 4)->notNull(),
            'UNIQUE KEY currency_day(currency_id, year, month, day)',
        ], $this->getTableOptions());
        $this->addForeignKey('currency_rate_currency', 'currency_rate', 'currency_id', 'currency', 'id', 'CASCADE', 'CASCADE');


        $this->createTable('account', [
            'id' => $this->primaryKey(),
            'currency_id' => $this->integer()->notNull(),
            'status' => "enum('active', 'disabled') not null default 'active'",
            'name' => $this->string(64)->notNull(),
            'bank' => $this->string(64),
            'account_number' => $this->string(64),
            'init_balance' => $this->decimal(12, 2)->notNull()->defaultValue('0.00'),
            'import_processor' => $this->string(),
            'notes' => $this->text(),
        ], $this->getTableOptions());
        $this->addForeignKey('account_currency', 'account', 'currency_id', 'currency', 'id', 'RESTRICT', 'RESTRICT');


        $this->createTable('counterparty_category', [
            'id' => $this->primaryKey(),
            'name' => $this->string(128)->notNull(),
            'notes' => $this->text(),
        ], $this->getTableOptions());


        $this->createTable('counterparty', [
            'id' => $this->primaryKey(),
            'category_id' => $this->integer(),
            'name' => $this->string(128)->notNull(),
            'notes' => $this->text(),
        ], $this->getTableOptions());
        $this->addForeignKey('counterparty_category_id', 'counterparty', 'category_id', 'counterparty_category', 'id', 'RESTRICT', 'RESTRICT');


        $this->createTable('classification_category', [
            'id' => $this->primaryKey(),
            'name' => $this->string(128)->notNull(),
            'notes' => $this->text(),
        ], $this->getTableOptions());


        $this->createTable('classification', [
            'id' => $this->primaryKey(),
            'category_id' => $this->integer(),
            'name' => $this->string(128)->notNull(),
            'type' => "enum('inflow', 'outflow', 'transfer', 'conversion')",
            'notes' => $this->text(),
        ], $this->getTableOptions());
        $this->addForeignKey('classification_category_id', 'classification', 'category_id', 'classification_category', 'id', 'RESTRICT', 'RESTRICT');


        $this->createTable('transaction', [
            'id' => $this->primaryKey(),
            'account_id' => $this->integer()->notNull(),
            'classification_id' => $this->integer()->notNull(),
            'counterparty_id' => $this->integer(),
            'user_id' => $this->integer()->notNull(),
            'related_id' => $this->integer(),
            'timestamp' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'inflow' => $this->decimal(12, 2)->notNull()->defaultValue('0.00'),
            'outflow' => $this->decimal(12, 2)->notNull()->defaultValue('0.00'),
            'opening_balance' => $this->decimal(12, 2)->notNull()->defaultValue('0.00'),
            'balance' => $this->decimal(12, 2)->notNull()->defaultValue('0.00'),
            'has_attachments' => $this->boolean()->notNull()->defaultValue(0),
            'description' => $this->text(),
        ], $this->getTableOptions());
        $this->addForeignKey('transaction_account', 'transaction', 'account_id', 'account', 'id', 'RESTRICT', 'RESTRICT');
        $this->addForeignKey('transaction_counterparty', 'transaction', 'counterparty_id', 'counterparty', 'id', 'RESTRICT', 'RESTRICT');
        $this->addForeignKey('transaction_classification', 'transaction', 'classification_id', 'classification', 'id', 'RESTRICT', 'RESTRICT');
        $this->addForeignKey('transaction_user', 'transaction', 'user_id', 'user', 'id', 'RESTRICT', 'RESTRICT');
        $this->addForeignKey('transaction_related', 'transaction', 'related_id', 'transaction', 'id', 'SET NULL', 'SET NULL');

    }

    public function down()
    {
        $this->dropTable('transaction');
        $this->dropTable('classification');
        $this->dropTable('classification_category');
        $this->dropTable('counterparty');
        $this->dropTable('counterparty_category');
        $this->dropTable('account');
        $this->dropTable('currency_rate');
        $this->dropTable('currency');
        $this->dropTable('form_template');
        $this->dropTable('attachment');
        $this->dropTable('user');
        $this->dropTable('language');
        $this->dropTable('setting');
        $this->dropTable('lookup');
    }

    protected function getTableOptions()
    {
        if ($this->db->driverName === 'mysql') {
            return 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
    }

}
