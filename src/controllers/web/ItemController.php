<?php

namespace portalium\menu\controllers\web;

use Yii;
use portalium\menu\Module;
use yii\filters\VerbFilter;
use portalium\base\Exception;
use portalium\menu\models\ItemChild;
use portalium\web\Controller;
use portalium\menu\models\Menu;
use yii\web\NotFoundHttpException;
use portalium\menu\models\MenuItem;
use yii\web\Response;
use yii\db\ActiveRecord;

/**
 * ItemController implements the CRUD actions for MenuItem model.
 */
class ItemController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all MenuItem models.
     *
     * @param int|null $id_menu
     * @return mixed
     */
    public function actionIndex($id_menu = null)
    {
        if (!Yii::$app->user->can('menuWebItemIndex') && !Yii::$app->user->can('menuWebItemIndexOwn')) {
            throw new \yii\web\ForbiddenHttpException(Module::t('You are not allowed to access this page.'));
        }
        return $this->redirect(['/menu/item/create', 'id_menu' => $id_menu]);
    }

    /**
     * Displays a single MenuItem model.
     * @param int $id Item ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        if (!Yii::$app->user->can('menuWebItemView')) {
            throw new \yii\web\ForbiddenHttpException(Module::t('You are not allowed to access this page.'));
        }
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new MenuItem model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @param int $id_menu The ID of the menu to which the item belongs.
     * @param int|null $id_item The ID of the item if an existing item is being used as a template.
     * @return string|Response
     * EN: Optimized Create action. Uses renderAjax for Pjax requests to avoid full layout rendering.
     */
    public function actionCreate($id_menu, $id_item = null)
    {
        if (!Yii::$app->user->can('menuWebItemCreate')) {
            throw new \yii\web\ForbiddenHttpException(Module::t('You are not allowed to access this page.'));
        }
        
        $model = new MenuItem();
        $model->style = '{"icon":"0xf0f6","color":"rgb(234, 153, 153)","iconSize":"24"}';
        
        if ($this->request->isPost) {
            $newItem = true;
            if ($id_item != null) {
                $model = MenuItem::findOne($id_item);
                if ($model == null) {
                    $model = new MenuItem();
                    $model->style = '{"icon":"0xf0f6","color":"rgb(234, 153, 153)","iconSize":"24"}';
                } else {
                    $newItem = false;
                }
            }
            if ($model->load($this->request->post())) {
                $model->id_menu = $id_menu;
                $id_parent = $this->request->post('MenuItem')['id_parent'];
                if ($newItem) {
                    $max = MenuItem::find()->max('sort');
                    $model->sort = $max + 1;
                }
                $itemChildModel = ItemChild::findOne(['id_child' => $model->id_item]);

                if ($model->save()) {
                    if ($id_parent) {
                        if ($itemChildModel) {
                            $itemChildModel->id_item = $id_parent;
                        } else {
                            $itemChildModel = new ItemChild(['id_item' => $id_parent, 'id_child' => $model->id_item]);
                        }
                        $itemChildModel->save();
                    } elseif ($itemChildModel) {
                        $itemChildModel->delete();
                    }
                    return;
                } else {
                    Yii::warning($model->getErrors(), 'menu');
                }
            }
        } else {
            $model->loadDefaultValues();
        }
        
        $menuModel = Menu::findOne($id_menu);

        // PERFORMANCE: If Pjax request, bypass layout (Sidebar, Navbar, Footer) for faster response.
        if ($this->request->isPjax) {
            return $this->renderAjax('create', [
                'model' => $model,
                'id_menu' => $id_menu,
                'menuModel' => $menuModel,
            ]);
        }

        return $this->render('create', [
            'model' => $model,
            'id_menu' => $id_menu,
            'menuModel' => $menuModel,
        ]);
    }

    /**
     * Updates an existing MenuItem model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id Item ID
     * @return string|Response
     * @throws NotFoundHttpException if the model cannot be found
     * EN: Optimized Update action. Implements renderAjax for Pjax requests to break the "Pjax Trap".
     */
    public function actionUpdate($id)
    {
        if (!Yii::$app->user->can('menuWebItemUpdate', ['model' => $this->findModel($id)])) {
            throw new \yii\web\ForbiddenHttpException(Module::t('You are not allowed to access this page.'));
        }
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['index', 'id_menu' => $model->id_menu]);
        }

        // PERFORMANCE: Use renderAjax instead of render to only send the form HTML back to the Pjax container
        if ($this->request->isPjax) {
            return $this->renderAjax('update', [
                'model' => $model,
                'id_menu' => $model->id_menu,
            ]);
        }

        return $this->render('update', [
            'model' => $model,
            'id_menu' => $model->id_menu,
        ]);
    }

    /**
     * Deletes an existing MenuItem model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @return Response|string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete()
    {
        $id_item = Yii::$app->request->post('id_item');

        if (!Yii::$app->user->can('menuWebItemDelete', ['model' => $this->findModel($id_item)])) {
            throw new \yii\web\ForbiddenHttpException(Module::t('You are not allowed to access this page.'));
        }
        $model = $this->findModel($id_item);

        $delete_type = Yii::$app->request->post('DynamicModel')['delete_type'];

        switch ($delete_type) {
            case 'delete':
                $this->deleteItem($id_item);
                break;
            case 'delete-and-move-sub-items':
                $id_parent = Yii::$app->request->post('DynamicModel')['id_parent'];
                $id_menu = Yii::$app->request->post('DynamicModel')['id_menu'];
                $this->deleteItem($id_item, $id_parent, $id_menu);
                break;
        }

        if (Yii::$app->request->isAjax) {
            return $this->asJson(['status' => 'success']);
        } else {
            return $this->redirect(['index', 'id_menu' => $model->id_menu]);
        }
    }

    /**
     * Clones an existing MenuItem model.
     * @return Response
     */
    public function actionClone()
    {
        if (!Yii::$app->user->can('menuWebItemClone')) {
            throw new \yii\web\ForbiddenHttpException(Module::t('You are not allowed to access this page.'));
        }
        if (Yii::$app->request->isAjax) {
            $id_menu = Yii::$app->request->post('DynamicModel')['id_menu'];
            $id_item = Yii::$app->request->post('id_item');
            try {
                $id_parent = Yii::$app->request->post('DynamicModel')['id_parent'];
            } catch (\Throwable $th) {
                $id_parent = null;
            }

            $menuModel = Menu::findOne($id_menu);
            $menuModel->addItem($id_item, true, $id_parent);
            return $this->asJson(['status' => 'success']);
        }
    }

    /**
     * Moves a MenuItem model.
     * @return Response
     */
    public function actionMove()
    {
        if (!Yii::$app->user->can('menuWebItemMove')) {
            throw new \yii\web\ForbiddenHttpException(Module::t('You are not allowed to access this page.'));
        }
        if (Yii::$app->request->isAjax) {
            $id_menu = Yii::$app->request->post('DynamicModel')['id_menu'];
            $id_item = Yii::$app->request->post('id_item');
            try {
                $id_parent = Yii::$app->request->post('DynamicModel')['id_parent'];
            } catch (\Throwable $th) {
                $id_parent = null;
            }
            $menuModel = Menu::findOne($id_menu);
            if ($menuModel->addItem($id_item, true, $id_parent)) {
                $item = MenuItem::findOne($id_item);
                try {
                    $item->deleteChildren();
                } catch (Exception $e) {
                    Yii::error($e->getMessage());
                }
                $item->delete();
                return $this->asJson(['status' => 'success']);
            }
        }
    }

    /**
     * Internal helper to delete an item and optionally reassign its children.
     * @param int $id_item
     * @param int|null $id_parent
     * @param int|null $id_menu
     */
    private function deleteItem($id_item, $id_parent = null, $id_menu = null)
    {
        $item = MenuItem::findOne($id_item);
        if ($item == null) {
            return;
        }

        if ($id_parent == null) {
            try {
                $item->deleteChildren();
            } catch (Exception $e) {
                Yii::error($e->getMessage());
            }
            $item->delete();
        } else {
            $items = ItemChild::find()->where(['id_item' => $id_item])->all();
            $targetItem = MenuItem::findOne($id_parent);
            $menu = Menu::findOne($id_menu);
            if ($menu == null) {
                return;
            }
            if ($targetItem == null) {
                foreach ($items as $value) {
                    $menu->addItem($value->id_child, true);
                }
            } else {
                foreach ($items as $value) {
                    $targetItem->addItem($value->id_child, true);
                }
            }

            try {
                $item->deleteChildren();
            } catch (Exception $e) {
                Yii::error($e->getMessage());
            }
            $item->delete();
        }
    }

    /**
     * Returns the types of routes available for a module via DepDrop.
     * @return string JSON
     */
    public function actionRouteType()
    {
        if (!Yii::$app->user->can('menuWebItemRouteType')) {
            throw new \yii\web\ForbiddenHttpException(Module::t('You are not allowed to access this page.'));
        }
        $out = [];
        if ($this->request->isPost) {
            $request = $this->request->post('depdrop_parents');
            $moduleName = $request[0];
            if (empty($moduleName)) {
                return $this->asJson(['output' => [], 'selected' => '']);
            }
            $module = Yii::$app->getModule($moduleName);
            $menuItems = $module->getMenuItems();
            
            foreach ($menuItems[0] as $value) {
                $out[] = ['id' => $value['type'], 'name' => $value['type']];
            }
            $out = array_unique($out, SORT_REGULAR);
            return json_encode(['output' => $out, 'selected' => '']);
        }
    }

    /**
     * Returns specific routes via DepDrop.
     * @return string JSON
     */
    public function actionRoute()
    {
        if (!Yii::$app->user->can('menuWebItemRoute')) {
            throw new \yii\web\ForbiddenHttpException(Module::t('You are not allowed to access this page.'));
        }
        $out = [];
        if ($this->request->isPost) {
            $request = $this->request->post('depdrop_parents');
            $moduleName = $request[0];
            $routeType = $request[1];
            if (empty($moduleName) || empty($routeType)) {
                return $this->asJson(['output' => [], 'selected' => '']);
            }
            $module = Yii::$app->getModule($moduleName);
            $menuItems = $module->getMenuItems();

            foreach ($menuItems[0] as $item) {
                if ($item['type'] == $routeType) {
                    switch ($routeType) {
                        case 'widget':
                            $out[] = ['id' => $item['label'], 'name' => $item['name']];
                            break;
                        case 'model':
                            $out[] = ['id' => $item['route'], 'name' => $item['class']];
                            break;
                        case 'action':
                            $out[] = ['id' => $item['route'], 'name' => $item['route']];
                            break;
                        case 'route':
                            $routes = $item['routes'];
                            foreach ($routes as $key => $route) {
                                $out[] = ['id' => $key, 'name' => $route];
                            }
                            break;
                    }
                }
            }
            return json_encode(['output' => $out, 'selected' => '']);
        }
    }

    /**
     * Returns model list via DepDrop.
     * @return string JSON
     */
    public function actionModel()
    {
        if (!Yii::$app->user->can('menuWebItemModel')) {
            throw new \yii\web\ForbiddenHttpException(Module::t('You are not allowed to access this page.'));
        }
        if ($this->request->isPost) {
            $request = $this->request->post('depdrop_parents');
            $moduleName = $request[0];
            $routeType = $request[1];
            $route = $request[2] ?? null;
            
            if (empty($moduleName) || empty($routeType) || in_array($routeType, ['widget', 'route', 'action'])) {
                return $this->asJson(['output' => [], 'selected' => '']);
            }
            
            $modelName = '';
            $module = Yii::$app->getModule($moduleName);
            $menuItems = $module->getMenuItems();
            $field = [];
            
            foreach ($menuItems[0] as $item) {
                if ($item['type'] == $routeType && $item['route'] == $route) {
                    $field = $item['field'];
                    $modelName = $item['class'];
                }
            }
            
            $data = (!empty($modelName)) ? $modelName::find()->select(['id' => $field['id'], 'name' => $field['name']])->asArray()->all() : [];
            return json_encode(['output' => $data, 'selected' => '']);
        }
    }

    /**
     * Returns a hierarchical list of parent items for DepDrop.
     * @return string JSON
     */
    public function actionParentList()
    {
        if (!Yii::$app->user->can('menuWebParentList')) {
            throw new \yii\web\ForbiddenHttpException(Module::t('You are not allowed to access this page.'));
        }

        if ($this->request->isPost) {
            $request = $this->request->post('depdrop_parents');
            $id_menu = isset($request[0]) ? $request[0] : null;

            if (empty($id_menu)) {
                return json_encode(['output' => [], 'selected' => '']);
            }

            $menu = Menu::findOne($id_menu);
            $menuItems = $menu->getItems()->orderBy(['sort' => SORT_ASC, 'id_item' => SORT_ASC])->asArray()->all();
            $menuRelations = ItemChild::find()->asArray()->all();

            $menuTree = [];
            foreach ($menuRelations as $relation) {
                $menuTree[$relation['id_item']][] = $relation['id_child'];
            }

            $sortedItems = [];
            foreach ($menuItems as $item) {
                $sortedItems[$item['id_item']] = $item;
            }

            $rootParents = array_diff(array_keys($sortedItems), array_column($menuRelations, 'id_child'));

            $menuHierarchicalList = [];
            foreach ($rootParents as $parentId) {
                if (isset($sortedItems[$parentId])) {
                    $parentItem = $sortedItems[$parentId];
                    $menuHierarchicalList[] = ['id' => $parentItem['id_item'], 'name' => $parentItem['label']];
                    $menuHierarchicalList = array_merge($menuHierarchicalList, $this->buildTree($parentItem['id_item'], $sortedItems, $menuTree, 1, false));
                }
            }

            $moveHierarchicalList = [];
            foreach ($rootParents as $parentId) {
                if (isset($sortedItems[$parentId])) {
                    $parentItem = $sortedItems[$parentId];
                    $moveHierarchicalList[] = ['id' => $parentItem['id_item'], 'name' => $parentItem['label']];
                    $moveHierarchicalList = array_merge($moveHierarchicalList, $this->buildTree($parentItem['id_item'], $sortedItems, $menuTree, 1, true));
                }
            }

            return json_encode([
                'output' => $moveHierarchicalList,
                'menu_output' => $menuHierarchicalList,
                'selected' => ''
            ]);
        }
    }

    /**
     * Recursive helper to build menu tree.
     */
    private function buildTree($parentId, $sortedItems, $menuTree, $depth = 0, $forMove = false)
    {
        $result = [];
        if (isset($menuTree[$parentId])) {
            usort($menuTree[$parentId], function ($a, $b) use ($sortedItems) {
                return ($sortedItems[$a]['sort'] ?? 0) - ($sortedItems[$b]['sort'] ?? 0);
            });

            foreach ($menuTree[$parentId] as $childId) {
                if (isset($sortedItems[$childId])) {
                    $childItem = $sortedItems[$childId];
                    $indentation = ($depth > 0 && $forMove) ? str_repeat('- ', $depth) : '';
                    $result[] = ['id' => $childItem['id_item'], 'name' => $indentation . $childItem['label']];
                    $result = array_merge($result, $this->buildTree($childItem['id_item'], $sortedItems, $menuTree, $depth + 1, $forMove));
                }
            }
        }
        return $result;
    }

    /**
     * Handles sorting of menu items.
     * @return string
     */
    public function actionSort()
    {
        if (!Yii::$app->user->can('menuWebItemSort')) {
            throw new \yii\web\ForbiddenHttpException(Module::t('You are not allowed to access this page.'));
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        $data = Yii::$app->request->post();
        MenuItem::sort($data);
        return "success";
    }

    /**
     * Finds the MenuItem model based on its primary key value.
     * @param int $id_item Item ID
     * @return MenuItem the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id_item)
    {
        if (($model = MenuItem::findOne(['id_item' => $id_item])) !== null) {
            return $model;
        }
        throw new NotFoundHttpException(Module::t('The requested page does not exist.'));
    }
}