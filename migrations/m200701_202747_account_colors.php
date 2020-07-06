<?php

use yii\db\Migration;

/**
 * Class m200701_202747_account_colors
 */
class m200701_202747_account_colors extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('account', 'label_enabled', $this->smallInteger(3)->notNull()->defaultValue(0));
        $this->addColumn('account', 'label_bg_color', $this->string(8));
        $this->addColumn('account', 'label_text_color', $this->string(8));

        $accountsQuery = (new \yii\db\Query())
            ->select(['id', 'notes'])
            ->from('account');

        $map = [
            'color' => 'label_text_color',
            'backcolor' => 'label_bg_color',
        ];

        foreach ($accountsQuery->each() as $account) {
            $result = [
                'label_bg_color' => '#777777',
                'label_text_color' => '#ffffff',
            ];
            foreach ($map as $from => $to) {
                $string = "[$from] => ";
                $res = strpos($account['notes'], $string);
                if ($res !== false) {
                    $result[$to] = substr($account['notes'], $res + strlen($string), 7);
                    $result['label_enabled'] = 1;
                }
            }
            if ($result) {
                $this->update('account', $result, ['id' => $account['id']]);
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn('account', 'label_enabled');
        $this->dropColumn('account', 'label_bg_color');
        $this->dropColumn('account', 'label_text_color');
    }
}
