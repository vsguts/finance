<?php

function getTextClass($value)
{
    if ($value > 0) {
        return 'text-success';
    } elseif ($value < 0) {
        return 'text-danger';
    }
}
