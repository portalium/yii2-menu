<?php

namespace diginova\menu;

class Module extends \portalium\base\Module
{
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
        self::registerTranslation('menu','@diginova/menu/messages',[
            'menu' => 'menu.php',
        ]);
    }

    public static function t($message, array $params = [])
    {
        return parent::coreT('menu', $message, $params);
    }
}