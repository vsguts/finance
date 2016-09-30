<?php

namespace app\assets;

use yii\helpers\Json;
use yii\web\AssetBundle;
use app\models\Account;
use app\models\Classification;

class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    
    public $css = [
        'css/site.less',
        'css/app.less',
    ];
    
    public $js = [
        'js/ajax.js',
        'js/core.js',
        'js/events.js',
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

    public function publish($am)
    {
        parent::publish($am);
        $this->css = $this->addLastModifiedParam($this->css);
        $this->js = $this->addLastModifiedParam($this->js);
    }

    protected function addLastModifiedParam($assets){
        foreach ($assets as $k => $asset) {
            $file_path = sprintf(
                '%s/%s',
                $this->basePath,
                $asset
            );

            $assets[$k] = sprintf(
                '%s?t=%s',
                $asset,
                filemtime($file_path)
            );
        }

        return $assets;
    }

}
