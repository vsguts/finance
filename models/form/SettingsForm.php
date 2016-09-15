<?php

namespace app\models\form;

use Yii;
use yii\base\Model;
use app\models\Setting;

/**
 * SettingsForm is the model behind the contact form.
 */
class SettingsForm extends Model
{
    /**
     * Settings
     */
    // General
    public $applicationName;
    public $companyName;
    public $baseUrl;
    public $poweredBy;
    public $adminEmail;
    public $supportEmail;
    public $defaultCurrency;
    
    public $mainpage_description;
    public $aboutpage_description;
    public $faqpage_description;
    
    // Mailer
    public $mailSendMethod;
    public $smtpHost;
    public $smtpUsername;
    public $smtpPassword;
    public $smtpEncrypt;

    // Bank Transactions
    public $transaction_update_days;


    public function behaviors()
    {
        return [
            [
                'class' => 'app\behaviors\LookupBehavior',
                'table' => 'setting',
            ],
        ];
    }

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [$this->attributes(), 'safe'],
            [
                ['adminEmail', 'supportEmail'],
                'email'
            ],
            [
                ['transaction_update_days'],
                'integer'
            ],
        ];
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            // General
            'applicationName' => __('Application name'),
            'baseUrl' => __('Base Url'),
            'poweredBy' => __('Powered by'),
            'adminEmail' => __('Admin email'),
            'supportEmail' => __('Support email'),
            'defaultCurrency' => __('Default currency'),
            
            // Descriptions
            'mainpage_description' => __('Main page'),
            'aboutpage_description' => __('About page'),
            'faqpage_description' => __('FAQ'),
            
            // Mailer
            'mailSendMethod' => __('Method of sending emails'),
            'smtpHost' => __('SMTP host'),
            'smtpUsername' => __('SMTP username'),
            'smtpPassword' => __('SMTP password'),
            'smtpEncrypt' => __('SMTP encrypted connection'),

            // Transactions
            'transaction_update_days' => __('Update days'),
            
        ];
    }

    public function init()
    {
        $settings = Setting::findAll($this->attributes());
        foreach ($settings as $setting) {
            $this->{$setting->name} = $setting->value;
        }
    }

    public function saveSettings()
    {
        $settings = [];
        foreach (Setting::findAll($this->attributes()) as $setting) {
            $settings[$setting->name] = $setting;
        }

        foreach ($this->attributes() as $attribute) {
            if (!empty($settings[$attribute])) {
                if ($settings[$attribute]->value != $this->$attribute) {
                    $settings[$attribute]->value = $this->$attribute;
                    $settings[$attribute]->save();
                }
            } else {
                $setting = new Setting;
                $setting->name = $attribute;
                $setting->value = $this->$attribute;
                $setting->save();
            }
        }

        // GC
        foreach (Setting::find()->where(['not in', 'name', $this->attributes()])->all() as $setting) {
            $setting->delete();
        }
    }
    
}
