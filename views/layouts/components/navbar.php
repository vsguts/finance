<?php

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
$controller_id = Yii::$app->controller->id;
$action_id = Yii::$app->controller->action->id;
$action_params = Yii::$app->controller->actionParams;

$is_profile = $controller_id == 'user' && $action_id == 'update' && $user->identity->id == $action_params['id'];

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
            'active' => $controller_id == 'transaction' && !in_array($action_id, ['report', 'import']),
        ],
        [
            'label' => __('Reports'),
            'url' => ['/transaction/report'],
            'visible' => $user->can('transaction_view'),
            'active' => $controller_id == 'transaction' && $action_id == 'report',
        ],
    ],
]);


/**
 * Right nav
 */

$menu_items = [];

if ($user->can('faq_page')) {
    $menu_items[] = [
        'label' => __('FAQ'),
        'url' => ['/site/faq'],
        'active' => $controller_id == 'site' && $action_id == 'faq',
    ];
}

// Registries
$items = [];
if ($user->can('account_view')) {
    $items[] = [
        'label' => __('Accounts'),
        'url' => ['/account/index'],
        'active' => $controller_id == 'account',
    ];
}
if ($user->can('classification_view')) {
    $items[] = [
        'label' => __('Classification'),
        'url' => ['/classification/index'],
        'active' => $controller_id == 'classification',
    ];
}

if ($user->can('counterparty_view')) {
    $items[] = [
        'label' => __('Counterparties'),
        'url' => ['/counterparty/index'],
        'active' => $controller_id == 'counterparty',
    ];
    $items[] = [
        'label' => __('Counterparty categories'),
        'url' => ['/counterparty-category/index'],
        'active' => $controller_id == 'counterparty-category',
    ];
}

if ($user->can('currency_view')) {
    if ($items) {
        $items[] = '<li class="divider"></li>';
    }
    $items[] = [
        'label' => __('Currencies'),
        'url' => ['/currency/index'],
        'active' => $controller_id == 'currency',
    ];
}

$menu_items[] = [
    'label' => __('Registries'),
    'visible' => !!$items,
    'active' => in_array($controller_id, [
        'account',
        'counterparty',
        'counterparty-category',
        'currency',
        'classification',
    ]),
    'items' => $items
];

// Administration
$items = [];

if ($user->can('user_view')) {
    if ($items) {
        $items[] = '<li class="divider"></li>';
    }
    $items[] = [
        'label' => __('Users'),
        'url' => ['/user/index'],
        'visible' => $user->can('user_view'),
        'active' => $controller_id == 'user' && !$is_profile,
    ];
}
if ($user->can('user_role_view')) {
    $items[] = [
        'label' => __('User roles'),
        'url' => ['/user-role/index'],
        'visible' => $user->can('user_role_view'),
        'active' => $controller_id == 'user-role',
    ];
}
if ($user->can('setting_view')) {
    if ($items) {
        $items[] = '<li class="divider"></li>';
    }
    $items[] = [
        'label' => __('Settings'),
        'url' => ['/setting/index'],
        'visible' => $user->can('setting_view'),
        'active' => $controller_id == 'setting',
    ];
}
$menu_items[] = [
    'label' => __('Administration'),
    'visible' => !!$items,
    'active' => !$is_profile && in_array($controller_id, [
        'setting',
        'weekend',
        'user',
        'user-role',
        'country',
        'import',
    ]),
    'items' => $items
];

// Account
$help_menu = [
    'label' => __('Help'),
    'items' => [
        'contact' => [
            'label' => __('Contact'),
            'url' => ['/site/contact'],
            'visible' => $user->can('contact_form'),
        ],
        'about' => [
            'label' => __('About'),
            'url' => ['/site/about'],
            'visible' => $user->can('about_page'),
        ],
    ],
    'active' => $controller_id == 'site' && in_array($action_id, ['contact', 'about']),
    'visible' => $user->can('contact_form') || $user->can('about_page'),
];

if ($user->isGuest) {

    $menu_items[] = ['label' => __('Signup'), 'url' => ['/site/signup']];
    $menu_items[] = ['label' => __('Login'), 'url' => ['/site/login']];

    $menu_items[] = $help_menu;

} else {

    $name = trim($user->identity->name);
    if (empty($name)) {
        $name = $user->identity->email;
    }
    $user_menu = [
        'label' => '<i class="glyphicon glyphicon-user"></i>',
        'active' => $is_profile || $help_menu['active'],
        'items' => [
            Html::tag('li', Html::a(__('Signed in as <br><b>{name}</b>', ['name' => $name])), ['class'=>'disabled']),
            '<li class="divider"></li>',
            [
                'label' => __('Profile'),
                'url' => ['/user/update', 'id' => $user->identity->id],
                'active' => $is_profile,
            ],
            [
                'label' => __('Logout'),
                'url' => ['/site/logout'],
                'linkOptions' => ['data-method' => 'post']
            ],
        ]
    ];
    if ($help_menu['visible']) {
        $user_menu['items'] = array_merge(
            $user_menu['items'],
            ['<li class="divider"></li>'],
            $help_menu['items']
        );
    }
    $menu_items[] = $user_menu;

}

echo Nav::widget([
    'options' => ['class' => 'navbar-nav navbar-right'],
    'encodeLabels' => false,
    'items' => $menu_items,
]);

/**
 * Search nav
 */

NavBar::end();
