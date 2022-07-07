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
        $data = json_decode($data['data'], true);
        Yii::warning($data);
        $index = 0;
        foreach ($data as $item) {
            $model = MenuItem::findOne($item['id']);
            if (!$model) {
                continue;
            }
            $model->sort = $index;
            $model->id_parent = 0;
            $model->save();
            $index++;
            if (isset($item['children'])) {
                $this->sortChildren($item['children'],$item['id'], $index);
            }
        }
        return "success";
    }

    public function sortChildren($children, $id_parent, &$index)
    {
        foreach ($children as $child) {
            //Yii::warning($child['id'].' '.$index);
            $model = MenuItem::findOne($child['id']);
            if (!$model) {
                
                continue;
            }
            $model->sort = $index;
            $model->id_parent = $id_parent;
            $model->save();
            $index++;
            if (isset($child['children'])) {
                $this->sortChildren($child['children'], $child['id'], $index);
            }

        }
    }


}