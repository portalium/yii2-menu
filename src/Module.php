<?php

namespace portalium\menu;

class Module extends \portalium\base\Module
{
    public static $tablePrefix = 'menu_';
    public $apiRules = [
        [
            'class' => 'yii\rest\UrlRule',
            'controller' => [
                'menu/default',
            ]
        ],
    ];

    public function getMenuItems()
    {
        $menuItems = [
            [
                [
                    'menu' => 'web',
                    'type' => 'model',
                    'class' => 'portalium\menu\models\MenuItem',
                    'route' => '/menu/item/view',
                    'field' => ['id' => 'id_item', 'name' => 'label'],
                ],
                [
                    'menu' => 'web',
                    'type' => 'widget',
                    'label' => '\portalium\site\widgets\LoginButton',
                    'name' => 'Login',
                ],
                [
                    'menu' => 'web',
                    'type' => 'action',
                    'route' => '/menu/default/index',
                ],
                [
                    'menu' => 'mobile',
                    'type' => 'route',
                    'route' => '/menu/default/index',
                    'routes' => [
                        '/auth/index' => 'Go to Index',
                        '/auth/login' => 'Go to Login',
                        '/auth/signup' => 'Go to Signup',
                        '/auth/company-login' => 'Go to Company Login',
                        '/auth/company-signup' => 'Go to Company Signup',
                        '/forgot' => 'Go to Forgot',
                        '/home' => 'Go to Home',
                        '/settings' => 'Go to Settings',
                        '/applications' => 'Go to Applications',
                        '/company-jobs' => 'Go to Company Jobs',
                        '/help' => 'Go to Help',
                        '/faq' => 'Go to Faq',
                        '/contract' => 'Go to Contract',
                        '/job-detail' => 'Go to Job Detail',
                        '/job-posting' => 'Go to Job Posting',
                        '/company-applications' => 'Go to Company Applications',
                        '/profile' => 'Go to Profile',
                        '/dashboard' => 'Go to Dashboard',
                        '/company-profile' => 'Go to Company Profile',
                        '/company-dashboard' => 'Go to Company Dashboard',
                        '/search' => 'Go to Search',
                        '/contact' => 'Go to Contact',
                    ],
                ],
                [
                    'menu' => 'mobile',
                    'type' => 'model',
                    'class' => 'portalium\content\models\Content',
                    'route' => 'Content',
                    'field' => ['id' => 'id_content', 'name' => 'title'],
                ],
                [
                    'menu' => 'mobile',
                    'type' => 'model',
                    'class' => 'portalium\content\models\Category',
                    'route' => 'Category',
                    'field' => ['id' => 'id_category', 'name' => 'name'],
                ],
            ],
        ];
        return $menuItems;
    }

    public static function moduleInit()
    {
        self::registerTranslation('menu', '@portalium/menu/messages', [
            'menu' => 'menu.php',
        ]);
    }

    public static function t($message, array $params = [])
    {
        return parent::coreT('menu', $message, $params);
    }
}