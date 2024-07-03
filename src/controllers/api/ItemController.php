<?php

namespace portalium\menu\controllers\api;

use portalium\menu\models\MenuItem;
use Yii;
use portalium\menu\Module;
use portalium\menu\models\Menu;
use portalium\rest\ActiveController as RestActiveController;

class ItemController extends RestActiveController
{
    public $modelClass = 'portalium\menu\models\MenuItem';

    public function actions()
    {
        $actions = parent::actions();
        $actions['index']['dataFilter'] = [
            'class' => \yii\data\ActiveDataFilter::class,
            'searchModel' => $this->modelClass,
        ];
        return $actions;
    }
}