<?php

namespace app\models\search;

use app\helpers\FileHelper;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;
use yii\base\Exception;


abstract class AbstractReportSearch extends TimestampSearch
{

    public $report;

    public function rules()
    {
        return array_merge(
            parent::rules(),
            [
                [['report', 'timestamp', 'timestamp_to'], 'safe'],
                [['report'], 'default', 'value' => $this->getDefaultReportName()],
            ]
        );
    }

    public function search($params)
    {
        $this->load($params);
        $this->validate();

        $report_class = '\app\components\reports\\' . $this->getReportsNamespace() . '\\' . Inflector::camelize($this->report);
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
        $path = '@app/components/reports/' . $this->getReportsNamespace();

        $reports = [];
        foreach (FileHelper::getPathClasses($path) as $id => $class) {
            $object = Yii::createObject($class);
            $reports[$id] = [
                'label' => $object->getReportName(),
                'position' => $object->position,
            ];
        }

        ArrayHelper::multisort($reports, ['position', 'label']);

        return $reports;
    }

    abstract protected function getReportsNamespace();

    abstract protected function getDefaultReportName();

}
