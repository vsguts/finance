<?php

namespace app\helpers;

class Text
{

    public static function prepareLines($text, $lower = false)
    {
        if (!$text) {
            return [];
        }

        $lines = explode("\n", $text);
        foreach ($lines as $k => $item) {
            $lines[$k] = trim($item);
            if ($lower) {
                $lines[$k] = strtolower($lines[$k]);
            }
            if (empty($lines[$k])) {
                unset($lines[$k]);
            }
        }
        return $lines;
    }
}
