<?php
namespace portalium\menu\bundles;

use yii\web\AssetBundle;

class MenuDepDropAsset extends AssetBundle
{
    public $sourcePath = '@vendor/portalium/yii2-menu/src/assets/';

    public $js = [
        'apps/custom/depdropMenu.js'
    ];

    public $depends = [
        'yii\web\YiiAsset'
    ];

    public $publishOptions = [
        'forceCopy' => YII_DEBUG
    ];

}