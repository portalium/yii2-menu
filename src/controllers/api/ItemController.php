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

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => \yii\filters\auth\HttpBearerAuth::class,
            'except' => ['sort'],
        ];
        return $behaviors;
    }

    public function actionSort()
    {
        $data = Yii::$app->request->post();
        Yii::warning($data);
        MenuItem::sort($data);
        return "success";
    }

}