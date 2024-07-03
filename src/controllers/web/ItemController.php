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
/**
 * MenuItemController implements the CRUD actions for MenuItem model.
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
     * @return string
     */
    public function actionIndex($id_menu = null)
    {
        if (!\Yii::$app->user->can('menuWebItemIndex') && !\Yii::$app->user->can('menuWebItemIndexOwn')) {
            throw new \yii\web\ForbiddenHttpException(Module::t('You are not allowed to access this page.'));
        }
        return $this->redirect(['/menu/item/create', 'id_menu' => $id_menu]);
    }

    /**
     * Displays a single MenuItem model.
     * @param int $id_item Item ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        if (!\Yii::$app->user->can('menuWebItemView')) {
            throw new \yii\web\ForbiddenHttpException(Module::t('You are not allowed to access this page.'));
        }
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }
    
    /**
     * Creates a new MenuItem model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate($id_menu, $id_item = null)
    {
        if (!\Yii::$app->user->can('menuWebItemCreate')) {
            throw new \yii\web\ForbiddenHttpException(Module::t('You are not allowed to access this page.'));
        }
        $model = new MenuItem();
        $model->style = '{"icon":"0xf0f6","color":"rgb(234, 153, 153)","iconSize":"24"}';
        if ($this->request->isPost) {
            $newItem = true;
            if ($id_item != null) {
                $model = MenuItem::findOne($id_item);
                if($model == null){
                    $model = new MenuItem();
                    $model->style = '{"icon":"0xf0f6","color":"rgb(234, 153, 153)","iconSize":"24"}';
                }else{
                    $newItem = false;
                }
            }
            if ($model->load($this->request->post())) {
                $model->id_menu = $id_menu;
                $id_parent = $this->request->post('MenuItem')['id_parent'];
                if($newItem){
                    $max = MenuItem::find()->max('sort');
                    $model->sort = $max + 1;
                }
                
                if($model->save()){
                    if($id_parent != null && $id_parent != 0){
                        $itemChildModel = ItemChild::findOne(['id_item' => $id_parent, 'id_child' => $model->id_item]);
                        if($itemChildModel == null){
                            $itemChildModel = new ItemChild();
                            $itemChildModel->id_item = $id_parent;
                            $itemChildModel->id_child = $model->id_item;
                            $itemChildModel->save();
                        }
                    }
                    return;
                }
            }
        } else {
            $model->loadDefaultValues();
        }
        $menuModel = Menu::findOne($id_menu);
        return $this->render('create', [
            'model' => $model,
            'id_menu' => $id_menu,
            'menuModel' => $menuModel,
        ]);
    }

    /**
     * Updates an existing MenuItem model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id_item Item ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        if (!\Yii::$app->user->can('menuWebItemUpdate', ['model' => $this->findModel($id)])) {
            throw new \yii\web\ForbiddenHttpException(Module::t('You are not allowed to access this page.'));
        }
        $model = $this->findModel($id);
        
        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['index', 'id_menu' => $model->id_menu]);
        }

        return $this->render('update', [
            'model' => $model,
            'id_menu' => $model->id_menu,
        ]);
    }

    /**
     * Deletes an existing MenuItem model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id_item Item ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete()
    {
        $id_item = Yii::$app->request->post('id_item');

        if (!\Yii::$app->user->can('menuWebItemDelete', ['model' => $this->findModel($id_item)])) {
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
            default:
                break;
        }

        if (Yii::$app->request->isAjax) {
            return $this->asJson(['status' => 'success']);
        } else {
            return $this->redirect(['index', 'id_menu' => $model->id_menu]);
        }
    }

    public function actionClone()
    {
        
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

    public function actionMove()
    {
        
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

    private function deleteItem($id_item, $id_parent = null, $id_menu = null)
    {
        $item = MenuItem::findOne($id_item);
        if($item == null){
            return;
        }

        if($id_parent == null){
            try {
                $item->deleteChildren();
            } catch (Exception $e) {
                Yii::error($e->getMessage());
            }
            $item->delete();
        }else if ($id_parent != null){
            $items = ItemChild::find()->where(['id_item' => $id_item])->all();
            $targetItem = MenuItem::findOne($id_parent);
            $menu = Menu::findOne($id_menu);
            if($menu == null){
                return;
            }
            if($targetItem == null){
                foreach ($items as $key => $value) {
                    $menu->addItem($value->id_child, true);
                }
            }else{
                foreach ($items as $key => $value) {
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

    public function actionRouteType() {
        if (!\Yii::$app->user->can('menuWebItemRouteType')) {
            throw new \yii\web\ForbiddenHttpException(Module::t('You are not allowed to access this page.'));
        }
        $out = [];
        if($this->request->isPost){
            $request = $this->request->post('depdrop_parents');
            $moduleName = $request[0];
            if($moduleName == null || $moduleName == ''){
                return $this->asJson(['output' => [], 'selected' => '']);
            }
            $module = Yii::$app->getModule($moduleName);
            $menuItems = $module->getMenuItems();
            
            foreach ($menuItems[0] as $key => $value) {
                    $out[] = ['id' => $value['type'], 'name' => $value['type']];
            }
            $out = array_unique($out, SORT_REGULAR);
            return json_encode(['output' => $out, 'selected' => '']);
        }
    }

    public function actionRoute() {
        if (!\Yii::$app->user->can('menuWebItemRoute')) {
            throw new \yii\web\ForbiddenHttpException(Module::t('You are not allowed to access this page.'));
        }
        $out = [];
        if($this->request->isPost){
            $request = $this->request->post('depdrop_parents');

            $moduleName = $request[0];
            $routeType = $request[1];
            if($moduleName == null || $routeType == null || $moduleName == '' || $routeType == ''){
                return $this->asJson(['output' => [], 'selected' => '']);
            }
            $module = Yii::$app->getModule($moduleName);
            $menuItems = $module->getMenuItems();

            foreach ($menuItems[0] as $key => $item) {
                if($item['type'] == $routeType){
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

    public function actionModel() {
        if (!\Yii::$app->user->can('menuWebItemModel')) {
            throw new \yii\web\ForbiddenHttpException(Module::t('You are not allowed to access this page.'));
        }
        $out = [];
        if($this->request->isPost){
            $request = $this->request->post('depdrop_parents');
            $moduleName = $request[0];
            $routeType = $request[1];
            $route = $request[2];
            if($moduleName == null || $routeType == null || $moduleName == '' || $routeType == '' || $routeType == 'widget' || $routeType == 'route' || $routeType == 'action'){
                return $this->asJson(['output' => [], 'selected' => '']);
            }
            if($moduleName == '' || $routeType == ''){
                return json_encode(['output' => $out, 'selected' => '']);
            }
            $modelName = '';
            $module = Yii::$app->getModule($moduleName);
            $menuItems = $module->getMenuItems();
            $field = [];
            
            foreach ($menuItems[0] as $key => $item) {
                if($item['type'] == $routeType && $item['route'] == $route){
                        $field = $item['field'];
                        $modelName = $item['class'];
                }
            }
            if ($modelName != '') {
                $data = $modelName::find()->select(['id' => $field['id'], 'name' => $field['name']])->asArray()->all();
            }
               else {
                    $data = [];
                }
            return json_encode(['output' => $data, 'selected' => '']);
        }
    }

    public function actionParentList(){
        $out = [];
        if($this->request->isPost){
            $request = $this->request->post('depdrop_parents');
            $id_menu = $request[0];
            if($id_menu == null || $id_menu == ''){
                return $this->asJson(['output' => [], 'selected' => '']);
            }
            $menu = Menu::findOne($id_menu);
            $menuItems = $menu->getItems()->asArray()->all();
            $out[] = ['id' => 0, 'name' => Module::t('Root')];
            foreach ($menuItems as $key => $item) {
                $out[] = ['id' => $item['id_item'], 'name' => $item['label']];
            }
            return json_encode(['output' => $out, 'selected' => '']);
        }
    }

    public function actionSort()
    {
        if (!\Yii::$app->user->can('menuWebItemSort')) {
            throw new \yii\web\ForbiddenHttpException(Module::t('You are not allowed to access this page.'));
        }
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $data = Yii::$app->request->post();
        MenuItem::sort($data);
        return "success";
    }

    /**
     * Finds the MenuItem model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
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
