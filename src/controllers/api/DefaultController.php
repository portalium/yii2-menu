<?php

namespace portalium\menu\controllers\api;

use portalium\menu\models\Menu;
use portalium\rest\ActiveController as RestActiveController;

class DefaultController extends RestActiveController
{
    public $modelClass = 'portalium\menu\models\Menu';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator']['except'] = ['items'];
        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);

        return $actions;
    }

    public function actionIndex($slug = null)
    {
        if ($slug == null) {
            $data = Menu::find()->all();
        } else {
            $data = Menu::find()->where(['slug' => $slug])->all();
        }
        return $data;
    }
}