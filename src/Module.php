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