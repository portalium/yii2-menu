<?php

namespace portalium\menu\controllers\api;

use Yii;
use portalium\menu\Module;
use portalium\menu\models\Menu;
use portalium\menu\models\MenuItem;
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

    public function actionMobile($slug = 'main')
    {
        $menu = Menu::find()->where(['slug' => $slug])->one();
        if ($menu === null) {
            throw new \yii\web\NotFoundHttpException(Module::t('Menu not found.'));
        }

        $items = MenuItem::find()
            ->where(['id_menu' => $menu->id_menu])
            ->orderBy('sort')
            ->all();

        $byId = [];
        $childIds = [];
        foreach ($items as $item) {
            $byId[$item->id_item] = $item;
            if (!empty($item->id_parent)) {
                $childIds[$item->id_parent][] = $item->id_item;
            }
        }

        $tree = [];
        foreach ($items as $item) {
            if (!empty($item->id_parent)) {
                continue;
            }
            $node = $this->buildMobileNode($item, $byId, $childIds);
            if ($node !== null) {
                $tree[] = $node;
            }
        }

        return [
            'success' => true,
            'slug' => $menu->slug,
            'id_workspace' => Yii::$app->workspace->id,
            'items' => $tree,
        ];
    }

    protected function buildMobileNode($item, array $byId, array $childIds)
    {
        if (!$this->isItemVisible($item)) {
            return null;
        }

        $children = [];
        if (isset($childIds[$item->id_item])) {
            foreach ($childIds[$item->id_item] as $childId) {
                if (!isset($byId[$childId])) {
                    continue;
                }
                $child = $this->buildMobileNode($byId[$childId], $byId, $childIds);
                if ($child !== null) {
                    $children[] = $child;
                }
            }
        }

        $target = $this->resolveTarget($item);

        if ($target['screen'] === null && empty($children) && $target['url'] === null) {
            return null;
        }

        return [
            'id' => (int) $item->id_item,
            'label' => $item->label,
            'icon' => $item->icon,
            'module' => $item->module,
            'screen' => $target['screen'],
            'url' => $target['url'],
            'params' => $target['params'],
            'permission' => $item->name_auth,
            'children' => $children,
        ];
    }

    protected function isItemVisible($item)
    {
        $nameAuth = $item->name_auth;

        if ($nameAuth === null || $nameAuth === '' || $nameAuth === 'guest') {
            return true;
        }

        $module = $item->module ?? null;

        return Yii::$app->user->can($nameAuth, ['id_module' => $module])
            || Yii::$app->workspace->can($module, $nameAuth)
            || Yii::$app->workspace->can($module, $nameAuth . 'Own');
    }

    protected function resolveTarget($item)
    {
        $result = ['screen' => null, 'url' => null, 'params' => []];

        if ($item->type == MenuItem::TYPE['url']) {
            $result['url'] = $item->url;
            return $result;
        }

        $route = null;
        if ($item->type == MenuItem::TYPE['route']) {
            $route = $item->url;
        } elseif ($item->type == MenuItem::TYPE['module']) {
            if ($item->routeType === 'widget') {
                return $result;
            }
            $route = $item->route;
        }

        if ($route === null || $route === '') {
            return $result;
        }

        $route = ltrim((string) $route, '/');
        $parts = array_values(array_filter(explode('/', $route), function ($part) {
            return $part !== '';
        }));

        if (count($parts) < 2) {
            return $result;
        }

        $module = $parts[0];
        $controller = $parts[1];
        $action = isset($parts[2]) ? $parts[2] : 'index';

        $result['screen'] = $module . '_' . $action;
        $result['params'] = [
            'module' => $module,
            'controller' => $controller,
            'action' => $action,
            'endpoint' => $module . '/' . $controller,
            'route' => $route,
        ];

        return $result;
    }

    public function beforeAction($action)
    {
        if (!parent::beforeAction($action)) {
            return false;
        }
        switch ($action->id) {
            case 'mobile':
                if (Yii::$app->user->isGuest)
                    throw new \yii\web\ForbiddenHttpException(Module::t('You do not have permission to view this menu.'));
                break;
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