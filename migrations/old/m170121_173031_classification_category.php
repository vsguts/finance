<?php

use yii\db\Migration;

class m170121_173031_classification_category extends Migration
{
    public function up()
    {
        $this->createTable('classification_category', [
            'id' => $this->primaryKey(),
            'name' => $this->string(128)->notNull(),
            'notes' => $this->text(),
        ], $this->getTableOptions());

        $this->addColumn('classification', 'category_id', $this->integer()->after('id'));
        $this->addForeignKey('classification_category_id', 'classification', 'category_id', 'classification_category', 'id', 'RESTRICT', 'RESTRICT');

        // Meanwhile
        $this->addForeignKey('counterparty_category_id', 'counterparty', 'category_id', 'counterparty_category', 'id', 'RESTRICT', 'RESTRICT');
    }

    public function down()
    {
        $this->dropForeignKey('classification_category_id', 'classification');
        $this->dropColumn('classification', 'category_id');

        $this->dropTable('classification_category');

        // Meanwhile
        $this->dropForeignKey('counterparty_category_id', 'counterparty');
    }

    protected function getTableOptions()
    {
        if ($this->db->driverName === 'mysql') {
            return 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
    }

}
