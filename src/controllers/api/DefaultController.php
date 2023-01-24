<?php

namespace portalium\menu\controllers\api;

use Yii;
use portalium\menu\Module;
use portalium\menu\models\Menu;
use portalium\menu\models\MenuSearch;
use portalium\rest\ActiveController as RestActiveController;

class DefaultController extends RestActiveController
{
    public $modelClass = 'portalium\menu\models\Menu';

    public function actions()
    {
        $actions = parent::actions();
        $actions['index']['dataFilter'] = [
            'class' => \yii\data\ActiveDataFilter::class,
            'searchModel' => $this->modelClass,
        ];

        $actions['index']['prepareDataProvider'] = function ($action) {
            $searchModel = new MenuSearch();
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
            if(!Yii::$app->user->can('menuApiDefaultIndex')){
                $dataProvider->query->andWhere(['id_user'=>Yii::$app->user->id]);
            }
            return $dataProvider;
        };
        return $actions;
    }

    public function beforeAction($action)
    {
        if (!parent::beforeAction($action)) {
            return false;
        }
        switch ($action->id) {
            case 'view':
                if (!Yii::$app->user->can('menuApiDefaultView')) 
                    throw new \yii\web\ForbiddenHttpException(Module::t('You do not have permission to view this menu.'));
                break;
            case 'create':
                if (!Yii::$app->user->can('menuApiDefaultCreate')) 
                    throw new \yii\web\ForbiddenHttpException(Module::t('You do not have permission to create this menu.'));
                break;
            case 'update':
                if (!Yii::$app->user->can('menuApiDefaultUpdate')) 
                    throw new \yii\web\ForbiddenHttpException(Module::t('You do not have permission to update this menu.'));
                break;
            case 'delete':
                if (!Yii::$app->user->can('menuApiDefaultDelete'))
                    throw new \yii\web\ForbiddenHttpException(Module::t('You do not have permission to delete this menu.'));
                break;
            default:
                if (!Yii::$app->user->can('menuApiDefaultIndex') && !Yii::$app->user->can('menuApiDefaultOwn'))
                    throw new \yii\web\ForbiddenHttpException(Module::t('You do not have permission to delete this menu.'));
                break;
        }
        
        return true;
    }

}