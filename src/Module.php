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

    public function getMenuItems(){
        $menuItems = [
            [
                [
                    'type' => 'model',
                    'class' => 'portalium\menu\models\MenuItem',
                    'route' => '/menu/item/view',
                    'field' => [ 'id' => 'id_item', 'name' => 'label' ],
                ],
                [
                    'type' => 'widget',
                    'label' => '\portalium\site\widgets\LoginButton',
                    'name' => 'Login',
                ],
                [
                    'type' => 'action',
                    'route' => '/menu/default/index',
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