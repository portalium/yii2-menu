<?php

namespace portalium\menu\bundles;

use yii\web\AssetBundle;

class DropMenuAsset extends AssetBundle
{
    public $sourcePath = '@vendor/portalium/yii2-menu/src/assets/';

    public $depends = [
        'portalium\theme\bundles\AppAsset'
    ];

    public $js = [
        'dropMenu.js'
    ];

    public $css = [
        'dropMenu.css'
    ];

    public $publishOptions = [
        'forceCopy' => YII_DEBUG
    ];

    public function init()
    {
        parent::init();
    }
}
