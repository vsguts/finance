<?php

namespace app\models\import\formatter;

use Yii;
use yii\base\Object;

abstract class AbstractFormatter extends Object
{
    public $encoding = 'UTF-8';

    public $delimiter = ';';

    public $has_cols = true;

    public function process($path)
    {
        $content = file_get_contents($path);

        if ($this->encoding != 'UTF-8') {
            $content = iconv('CP1251', 'UTF-8', $content);
        }

        return $this->parse($content);
    }

    abstract protected function parse($content);

}
