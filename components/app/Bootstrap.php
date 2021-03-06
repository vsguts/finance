<?php

namespace app\components\app;

use Yii;
use yii\di\ServiceLocator;
use app\models\Setting;

class Bootstrap extends ServiceLocator
{
    public function init()
    {
        // Settings
        $settings = [];
        try {
            $settings = Setting::settings();
        } catch (\Throwable $e) {
            Yii::error('Error get settings: ' . $e->getMessage());
        }
        Yii::$app->params = array_merge(Yii::$app->params, $settings);
        
        // Base Url
        $request = Yii::$app->getRequest();
        if (!($request instanceof \yii\web\Request)) {
            $urlManager = Yii::$app->components['urlManager'];
            $urlManager['baseUrl'] = Yii::$app->params['baseUrl'];
            Yii::$app->set('urlManager', $urlManager);
        }

        // Mailer
        $mailer = Yii::$app->components['mailer'];
        $mailSendMethod = $settings['mailSendMethod'] ?? '';
        if ($mailSendMethod == 'file') {
            $mailer['useFileTransport'] = true;
        } elseif ($mailSendMethod == 'smtp') {
            if (strpos($settings['smtpHost'], ':')) {
                list($settings['smtpHost'], $port) = explode(':', $settings['smtpHost']);
            } else {
                $port = 25;
                if ($settings['smtpEncrypt'] == 'ssl') {
                    $port = 465;
                } elseif ($settings['smtpEncrypt'] == 'tls') {
                    $port = 587;
                }
            }
            $mailer['transport'] = [
                'class'      => 'Swift_SmtpTransport',
                'host'       => $settings['smtpHost'],
                'username'   => $settings['smtpUsername'],
                'password'   => $settings['smtpPassword'],
                'port'       => $port,
                'encryption' => $settings['smtpEncrypt'] == 'none' ? null : $settings['smtpEncrypt'],
            ];
        }
        Yii::$app->set('mailer', $mailer);
    }

}
