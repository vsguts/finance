<?php

namespace app\models\query;

class LanguageQuery extends ActiveQuery
{
    public function sorted($order = SORT_ASC)
    {
        return $this->orderBy(['name' => $order]);
    }

}
