<?php

namespace app\models\import\formatter;

use Yii;

class Csv extends AbstractFormatter
{
    public function parse($content)
    {
        $path = Yii::getAlias('@runtime/importfile');
        file_put_contents($path, $content);
        $handle = fopen($path, 'r');
        
        if ($this->has_cols) {
            $cols = fgetcsv($handle, 0, $this->delimiter);
        }

        $result = [];

        while ($row = fgetcsv($handle, 0, $this->delimiter)) {
            if ($this->has_cols) {
                if (count($cols) < count($row)) {
                    $row = array_slice($row, 0, count($cols));
                }
                $row = array_combine($cols, $row);
            }

            $result[] = $row;
        }
        unlink($path);
        
        return $result;
    }
}
