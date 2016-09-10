<?php

use app\models\Account;

echo $form
    ->field($model, 'data[accounts]')
    ->label(__('Accounts'))
    ->checkboxList(
        Account::find()->scroll(['all' => true, 'all_key' => 'all']),
        [
            // 'class' => 'app-checkboxes-group-allow',
            // 'unselect' => null,
        ]
    )
;
