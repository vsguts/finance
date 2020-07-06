<?php

namespace app\models\contracts;


interface LabeledModel
{
    public function getLabelEnabled() : bool;
    public function getLabelBgColor() : string;
    public function getLabelTextColor() : string;
}
