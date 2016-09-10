<?php

namespace app\models\import;

use Yii;
use yii\base\Model;

abstract class AbstractImport extends Model
{
    public $viewPath = 'common';

    public $delimiter;

    public $upload;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['delimiter', 'upload'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'delimiter' => __('Delimiter'),
            'upload' => __('Upload'),
        ];
    }

    public function import($path)
    {
        $data = $this->getFormatter()->process($path);
        $data = $this->prepareData($data);
        
        $imported = 0;
        foreach ($data as $row) {
            $res = $this->processData($row);
            if ($res) {
                $imported ++;
            }
        }
        return $imported;
    }

    abstract protected function processData($data);

    public function getAvailableDelimiters()
    {
        return [
            ';' => __('Semicolon'),
            ',' => __('Comma'),
        ];
    }

    public function getFormat()
    {
        return 'csv';
    }

    public function getEncoding()
    {
        return 'UTF-8';
    }

    public function getDelimiter()
    {
        return $this->delimiter;
    }

    public function getHasCols()
    {
        return true;
    }

    protected function prepareData($data)
    {
        return $data;
    }

    protected function getFormatter()
    {
        return Yii::createObject([
            'class' => 'app\models\import\formatter\\' . ucfirst($this->getFormat()),
            'encoding' => $this->getEncoding(),
            'has_cols' => $this->getHasCols(),
            'delimiter' => $this->getDelimiter(),
        ]);
    }

}
