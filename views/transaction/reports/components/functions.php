<?php

function getTextClass($value, $reverse = false)
{
    if ($reverse) {
        $value = - $value;
    }
    if ($value > 0) {
        return 'text-success';
    } elseif ($value < 0) {
        return 'text-danger';
    }
}
