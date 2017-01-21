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


$proccess_menu_item = function ($menu_item) {
    $items = isset($menu_item['items']) ? $menu_item['items'] : [];
    $sections = isset($menu_item['sections']) ? $menu_item['sections'] : [];

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

    $menu_item['active'] = false;
    $menu_item['visible'] = false;

    if ($items) {
        foreach ($items as $item) {
            if (isset($item['active']) && $item['active']) {
                $menu_item['active'] = true;
            }
            if (isset($item['visible']) && $item['visible']) {
                $menu_item['visible'] = true;
            }
        }
    }
    $menu_item['items'] = $items;

    return $menu_item;
};


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

$menu_items = [
    $proccess_menu_item([
        'label' => __('Registries'),
        'sections' => [
            [
                [
                    'label' => __('Accounts'),
                    'url' => ['/account/index'],
                    'visible' => $user->can('account_view'),
                    'active' => $controller_id == 'account',
                ]
            ],
            [
                [
                    'label' => __('Classification'),
                    'url' => ['/classification/index'],
                    'visible' => $user->can('classification_view'),
                    'active' => $controller_id == 'classification',
                ],
                [
                    'label' => __('Classification categories'),
                    'url' => ['/classification-category/index'],
                    'visible' => $user->can('classification_view'),
                    'active' => $controller_id == 'classification-category',
                ],
            ],
            [
                [
                    'label' => __('Counterparties'),
                    'url' => ['/counterparty/index'],
                    'visible' => $user->can('counterparty_view'),
                    'active' => $controller_id == 'counterparty',
                ],
                [
                    'label' => __('Counterparty categories'),
                    'url' => ['/counterparty-category/index'],
                    'visible' => $user->can('counterparty_view'),
                    'active' => $controller_id == 'counterparty-category',
                ],
            ],
            [
                [
                    'label' => __('Currencies'),
                    'url' => ['/currency/index'],
                    'visible' => $user->can('currency_view'),
                    'active' => $controller_id == 'currency',
                ],
            ],
        ],
    ]),
    // Administration
    $proccess_menu_item([
        'label'   => __('Administration'),
        'sections'   => [
            [
                [
                    'label'   => __('Users'),
                    'url'     => ['/user/index'],
                    'visible' => $user->can('user_view'),
                    'active'  => $controller_id == 'user',
                ],
                [
                    'label'   => __('User roles'),
                    'url'     => ['/user-role/index'],
                    'visible' => $user->can('user_role_view'),
                    'active'  => $controller_id == 'user-role',
                ]
            ],
            [
                [
                    'label'   => __('Settings'),
                    'url'     => ['/setting/index'],
                    'visible' => $user->can('setting_view'),
                    'active'  => $controller_id == 'setting',
                ]
            ],
        ],
    ]),
    // Help
    $proccess_menu_item([
        'label' => __('Help'),
        'sections' => [
            [
                [
                    'label'   => __('Currency rates'),
                    'url'     => ['/currency/rates'],
                    'visible' => $user->can('currency_view'),
                    'active'  => $controller_id == 'currency' && $action_id == 'rates',
                ]
            ],
            [
                [
                    'label'   => __('FAQ'),
                    'url'     => ['/site/faq'],
                    'visible' => $user->can('faq_page'),
                    'active'  => $controller_id == 'site' && $action_id == 'faq',
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

// Account

if ($user->isGuest) {
    $menu_items[] = ['label' => __('Signup'), 'url' => ['/site/signup']];
    $menu_items[] = ['label' => __('Login'), 'url' => ['/site/login']];
} else {
    $name = trim($user->identity->name);
    if (empty($name)) {
        $name = $user->identity->email;
    }
    $menu_items[] = $proccess_menu_item([
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
    'items' => $menu_items,
]);

/**
 * Search nav
 */

NavBar::end();
