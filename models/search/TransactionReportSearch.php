<?php

namespace app\models\search;

use Yii;
use yii\helpers\Inflector;
use yii\base\Model;
use yii\base\Exception;
use yii\data\ActiveDataProvider;
use app\models\Transaction;

class TransactionReportSearch extends Model
{

    public $report;

    public $timestamp;

    public $timestamp_to;

    /**
     * @inheritdoc
     */
    public function formName()
    {
        return '';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'app\behaviors\SearchBehavior',
            [
                'class' => 'app\behaviors\TimestampConvertBehavior',
                'fields' => ['timestamp', 'timestamp_to']
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $today = Yii::$app->formatter->asTimestamp(date('Y-m-d 00:00'));

        return [
            [['report', 'timestamp', 'timestamp_to'], 'safe'],

            [['report'], 'default', 'value' => 'account-turnovers'],
            [['timestamp'], 'default', 'value' => $today - 30 * SECONDS_IN_DAY],
            [['timestamp_to'], 'default', 'value' => $today],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'timestamp' => __('Time'),
            'timestamp_to' => __('Time to'),
        ];
    }

    public function search($params)
    {
        $this->load($params);
        $this->validate();

        $report_class = '\app\components\transactionReports\\' . Inflector::camelize($this->report);
        if (!class_exists($report_class)) {
            throw new Exception('Class ' . $report_class . ' not found');
        }

        $params = $this->attributes;
        unset($params['report']);
        $params['class'] = $report_class;

        return Yii::createObject($params);
    }

    public function getReports()
    {
        $path = '@app/components/transactionReports';
        $dir = Yii::getAlias($path);
        $namespace = strtr($path, ['@' => '', '/' => '\\']) . '\\';

        $reports = [];
        foreach (scandir($dir) as $file) {
            if (in_array($file, ['.', '..'])) {
                continue;
            }
            $file = str_replace('.php', '', $file);
            $class = $namespace . $file;
            if (class_exists($class)) {
                if ((new \ReflectionClass($class))->isAbstract()) {
                    continue;
                }
                $object = Yii::createObject($class);
                $reports[Inflector::camel2id($file)] = __($object->report_name);
            }
        }

        asort($reports);

        return $reports;
    }

}
