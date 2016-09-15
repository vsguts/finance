<?php

use app\models\Currency;

echo $form->field($model, 'applicationName');
echo $form->field($model, 'companyName');
echo $form->field($model, 'baseUrl');
echo $form->field($model, 'poweredBy');
echo $form->field($model, 'adminEmail');
echo $form->field($model, 'supportEmail');

echo Html::tag('h4', __('Currencies:'));

echo $form->field($model, 'defaultCurrency')->dropDownList(Currency::find()->scroll(['empty' => true]));
