<?php

use app\models\Language;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;


NavBar::begin([
    'brandLabel' => Yii::$app->params['applicationName'],
    'brandUrl' => Yii::$app->homeUrl,
    'options' => [
        'class' => 'navbar-inverse navbar-fixed-top',
    ],
    'innerContainerOptions' => [
        'class' => 'container-fluid',
    ],
]);

$user = Yii::$app->user;
$controllerId = Yii::$app->controller->id;
$actionId = Yii::$app->controller->action->id;
$actionParams = Yii::$app->controller->actionParams;


$processMenuItem = function ($menuItem) {
    $items = isset($menuItem['items']) ? $menuItem['items'] : [];
    $sections = isset($menuItem['sections']) ? $menuItem['sections'] : [];

    if ($sections) {
        foreach ($sections as $key => $section) {
            $sections[$key] = array_filter(
                $section,
                function ($item) {
                    return !isset($item['visible']) || $item['visible'];
                }
            );

            if (!$sections[$key]) {
                unset($sections[$key]);
            }
        }
        $is_first = true;
        foreach ($sections as $section) {
            if (!$is_first) {
                array_unshift($section, '<li class="divider"></li>');
            }
            $is_first = false;
            $items = array_merge($items, $section);
        }
    }

    $menuItem['active'] = false;
    $menuItem['visible'] = false;

    if ($items) {
        foreach ($items as $item) {
            if (isset($item['active']) && $item['active']) {
                $menuItem['active'] = true;
            }
            if (isset($item['visible']) && $item['visible']) {
                $menuItem['visible'] = true;
            }
        }
    }
    $menuItem['items'] = $items;

    return $menuItem;
};


$is_profile = $controllerId == 'user' && $actionId == 'update' && $user->identity->id == $actionParams['id'];

/**
 * Left nav
 */

echo Nav::widget([
    'options' => ['class' => 'navbar-nav navbar-left'],
    'encodeLabels' => false,
    'items' => [
        [
            'label' => __('Transactions'),
            'url' => ['/transaction/index'],
            'visible' => $user->can('transaction_view'),
            'active' => $controllerId == 'transaction' && !in_array($actionId, ['report', 'import']),
        ],
        [
            'label'   => __('Reports'),
            'url'     => ['/reports/account-turnovers/view'],
            'visible' => $user->can('transaction_view'),
            'active'  => strpos($controllerId, 'reports/') === 0,
        ],
        [
            'label' => Html::tag('b', __('Partners')),
            'url' => ['/partner/index'],
            'visible' => $user->can('partner_view') || $user->can('partner_view_own'),
            'active' => $controllerId == 'partner',
        ],
        [
            'label' => __('Communication'),
            'url' => ['/communication/index'],
            'visible' => $user->can('communication_view') || $user->can('communication_view_own'),
            'active' => $controllerId == 'communication',
        ],
        [
            'label' => __('Tasks'),
            'url' => ['/task/index'],
            'visible' => $user->can('task_view') || $user->can('task_view_own'),
            'active' => $controllerId == 'task',
        ],
    ],
]);


/**
 * Right nav
 */

$menuItems = [
    $processMenuItem([
        'label' => __('Registries'),
        'sections' => [
            [
                [
                    'label' => __('Accounts'),
                    'url' => ['/account/index'],
                    'visible' => $user->can('account_view'),
                    'active' => $controllerId == 'account',
                ]
            ],
            [
                [
                    'label' => __('Classifications'),
                    'url' => ['/classification/index'],
                    'visible' => $user->can('classification_view'),
                    'active' => $controllerId == 'classification',
                ],
                [
                    'label' => __('Classification categories'),
                    'url' => ['/classification-category/index'],
                    'visible' => $user->can('classification_view'),
                    'active' => $controllerId == 'classification-category',
                ],
            ],
            [
                [
                    'label' => __('Counterparties'),
                    'url' => ['/counterparty/index'],
                    'visible' => $user->can('counterparty_view'),
                    'active' => $controllerId == 'counterparty',
                ],
                [
                    'label' => __('Counterparty categories'),
                    'url' => ['/counterparty-category/index'],
                    'visible' => $user->can('counterparty_view'),
                    'active' => $controllerId == 'counterparty-category',
                ],
            ],
            [
                [
                    'label' => __('Currencies'),
                    'url' => ['/currency/index'],
                    'visible' => $user->can('currency_view'),
                    'active' => $controllerId == 'currency',
                ],
                [
                    'label' => __('Countries'),
                    'url' => ['/country/index'],
                    'visible' => $user->can('country_view'),
                    'active' => $controllerId == 'country',
                ],
                [
                    'label' => __('States'),
                    'url' => ['/state/index'],
                    'visible' => $user->can('state_view'),
                    'active' => $controllerId == 'state',
                ],
            ],
        ],
    ]),
    // Administration
    $processMenuItem([
        'label'   => __('Administration'),
        'sections'   => [
            [
                [
                    'label'   => __('Users'),
                    'url'     => ['/user/index'],
                    'visible' => $user->can('user_view'),
                    'active'  => $controllerId == 'user',
                ],
                [
                    'label'   => __('User roles'),
                    'url'     => ['/user-role/index'],
                    'visible' => $user->can('user_role_view'),
                    'active'  => $controllerId == 'user-role',
                ]
            ],
            [
                [
                    'label'   => __('Settings'),
                    'url'     => ['/setting/index'],
                    'visible' => $user->can('setting_view'),
                    'active'  => $controllerId == 'setting',
                ]
            ],
        ],
    ]),
    // Help
    $processMenuItem([
        'label' => __('Help'),
        'sections' => [
            [
                [
                    'label'   => __('FAQ'),
                    'url'     => ['/site/faq'],
                    'visible' => $user->can('faq_page'),
                    'active'  => $controllerId == 'site' && $actionId == 'faq',
                ],
                [
                    'label'   => __('Contact'),
                    'url'     => ['/site/contact'],
                    'visible' => $user->can('contact_form'),
                ],
                [
                    'label'   => __('About'),
                    'url'     => ['/site/about'],
                    'visible' => $user->can('about_page'),
                ],
            ]
        ],
    ]),
];


// Languages
$selectLanguage = false;
$langItems = [];
foreach (Language::find()->sorted()->all() as $language) {
    if ($language->code == Yii::$app->language) {
        $selectLanguage = $language;
        // break;
    }
    $langItems[] = [
        'label' => $language->name,
        'url' => ['language/select', 'id' => $language->id, 'current_url' => Url::to()],
        'active' => $language == $selectLanguage,
    ];
}
if (!$selectLanguage) {
    $selectLanguage = Language::find()->where(['code' => 'en-US'])->one();
}
$menuItems[] = ['label' => $selectLanguage->short_name, 'items' => $langItems];


// Account

if ($user->isGuest) {
    $menuItems[] = ['label' => __('Signup'), 'url' => ['/site/signup']];
    $menuItems[] = ['label' => __('Login'), 'url' => ['/site/login']];
} else {
    $name = trim($user->identity->name);
    if (empty($name)) {
        $name = $user->identity->email;
    }
    $menuItems[] = $processMenuItem([
        'label' => '<i class="glyphicon glyphicon-user"></i>',
        'items' => [
            Html::tag(
                'li',
                Html::a(__('Signed in as <br><b>{name}</b>', ['name' => $name])),
                ['class' => 'disabled']
            ),
            '<li class="divider"></li>',
            [
                'label'   => __('Profile'),
                'url'     => ['/user/update', 'id' => $user->id, '_return_url' => Url::to()],
                'visible' => true,
                'linkOptions' => [
                    'class' => 'app-modal',
                    'data-target-id' => 'user_' . $user->id,
                ],
            ],
            [
                'label'       => __('Logout'),
                'url'         => ['/site/logout'],
                'linkOptions' => ['data-method' => 'post'],
                'visible'     => true,
            ],
        ]
    ]);
}

echo Nav::widget([
    'options' => ['class' => 'navbar-nav navbar-right'],
    'encodeLabels' => false,
    'items' => $menuItems,
]);

/**
 * Search nav
 */

NavBar::end();
