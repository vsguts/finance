<?php

use yii\db\Migration;

/**
 * Class m210417_135218_images
 */
class m210417_135218_images extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('image', [
            'id'          => $this->primaryKey(),
            'table'       => $this->string()->notNull(),
            'object_id'   => $this->integer()->notNull(),
            'object_type' => $this->string(32)->notNull()->defaultValue('main'),
            'filename'    => $this->string()->notNull(),
            'default'     => $this->integer(),
        ], $this->getTableOptions());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('image');
    }

    protected function getTableOptions()
    {
        if ($this->db->driverName === 'mysql') {
            return 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
    }
}
