<?php

namespace app\components\export\formatter;

use app\helpers\FileHelper;
use Yii;
use yii\base\BaseObject;

abstract class AbstractFormatter extends BaseObject
{
    public $columns = [];

    public $data = [];

    public $filename = 'export';

    public $cachePath = '@runtime/export';

    /**
     * @param boolean $download
     * @return mixed
     */
    abstract public function export($download = false);

    /**
     * @param string $filename
     * @param bool   $createDir
     * @return string Absolute file path
     */
    public function getFilePath($filename, $createDir = false)
    {
        $path = Yii::getAlias($this->cachePath);
        if ($createDir) {
            FileHelper::createDirectory($path, 0777, true);
        }
        return $path . '/' . $filename;
    }
}
