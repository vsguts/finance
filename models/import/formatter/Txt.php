<?php

namespace app\models\import\formatter;

use Yii;

class Txt extends AbstractFormatter
{
    public function parse($content)
    {
        $result = [];

        foreach (explode($this->delimiter, $content) as $block) {
            $data = [];
            foreach (explode("\n", $block) as $row) {
                $row = trim($row);
                if (strpos($row, '=')) {
                    list($key, $value) = explode('=', $row);
                    $data[trim($key)] = trim($value);
                }
            }

            $result[] = $data;
        }
        
        return $result;
    }
}
