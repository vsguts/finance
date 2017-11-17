<?php

namespace app\assets;

use app\models\Account;
use app\models\Classification;
use yii\web\AssetBundle;

class AppAsset extends AssetBundle
{
    public $sourcePath = '@app/assets/app';
    
    public $css = [
        'css/site.less',
        'css/app.less',
    ];
    
    public $js = [
        'js/jq-extend.js',
        'js/jq-fn-extend.js',
        'js/events.js',
        'js/ajax.js',
    ];
    
    public $depends = [
        'yii\web\YiiAsset',
        'yii\widgets\ActiveFormAsset',
        'yii\validators\ValidationAsset',
        'yii\bootstrap\BootstrapAsset',
        'yii\bootstrap\BootstrapPluginAsset',
        'app\assets\BowerAsset',
        'kartik\base\WidgetAsset',
        'kartik\select2\Select2Asset',
        'kartik\date\DatePickerAsset',
        'bluezed\floatThead\FloatTheadAsset',
    ];

    /**
     * Put translates into JS
     *
     * @todo Cash it
     */
    public static function appLangs()
    {
        $translates = [];
        
        // Langs
        $langs = [
            'No items selected',
        ];
        foreach ($langs as $lang) {
            $translates[$lang] = __($lang);
        }

        return self::encodeData('langs', $translates);
    }

    public static function appAccounts()
    {
        $hash_accounts = [];
        $hash_account_currencies = [];
        $accounts = Account::find()->sorted()->all();
        foreach ($accounts as $account) {
            $hash_accounts[] = [
                'id' => $account->id,
                'name' => $account->name,
            ];
            $hash_account_currencies[$account->id] = $account->currency->id;
        }

        return implode(PHP_EOL, [
            self::encodeData('accounts', $hash_accounts),
            self::encodeData('account_currencies', $hash_account_currencies),
        ]);
    }

    public static function appClassifications()
    {
        return self::encodeData('classifications', Classification::find()->simple()->sorted()->all());
    }

    protected static function encodeData($var, $data)
    {
        return sprintf('window.yii.app.%s = %s;', $var, json_encode($data, JSON_UNESCAPED_UNICODE));
    }

}
