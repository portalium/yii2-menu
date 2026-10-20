<?php

namespace portalium\menu;

use portalium\base\Event;
use portalium\menu\components\TriggerActions;
use portalium\user\Module as UserModule;

class Module extends \portalium\base\Module
{
    public static $tablePrefix = 'menu_';

    public static $name = 'Menu';
    
    public $apiRules = [
        [
            'class' => 'yii\rest\UrlRule',
            'controller' => [
                'menu/default',
                'menu/item',
            ],
            'pluralize' => false,
            'extraPatterns' => [
                'GET mobile' => 'mobile',
            ],
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

    public function registerEvents()
    {
        Event::on($this::className(), UserModule::EVENT_USER_DELETE_BEFORE, [new TriggerActions(), 'onUserDeleteBefore']);
    }
}
