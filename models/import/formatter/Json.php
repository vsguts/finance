<?php

namespace app\models\import\formatter;

use Yii;

class Json extends AbstractFormatter
{
    public function parse($content)
    {
        return json_decode($content, true);
    }
}
