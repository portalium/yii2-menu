<?php

namespace portalium\menu\controllers\api;

use portalium\menu\Module;
use portalium\rest\ActiveController as RestActiveController;
use Yii;
use yii\web\ForbiddenHttpException;

/**
 * Menu Item Controller implements the CRUD actions for MenuItem model.
 */
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

    public function checkAccess($action, $model = null, $params = [])
    {
        $isRead = in_array($action, ['index', 'view'], true);
        $allowed = $isRead
            ? Yii::$app->user->can('menuApiDefaultView') || Yii::$app->user->can('menuApiDefaultIndex')
            : Yii::$app->user->can('menuApiDefaultUpdate');
        if (!$allowed) {
            throw new ForbiddenHttpException(Module::t('You are not allowed to access this page.'));
        }
    }
}
