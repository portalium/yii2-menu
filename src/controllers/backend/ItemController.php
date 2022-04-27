<?php

namespace portalium\menu\controllers\backend;

use Yii;
use portalium\menu\Module;
use yii\filters\VerbFilter;
use portalium\web\Controller;
use portalium\menu\models\Menu;
use yii\web\NotFoundHttpException;
use portalium\menu\models\MenuItem;
use portalium\menu\models\MenuItemSearch;

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
        if (!\Yii::$app->user->can('menuBackendItemIndex')) {
            throw new \yii\web\ForbiddenHttpException(Module::t('You are not allowed to access this page.'));
        }
        $searchModel = new MenuItemSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        if ($id_menu != null) {
            $dataProvider->query->andWhere(['id_menu' => $id_menu]);
            $name = Menu::findOne($id_menu)->name;
            return $this->render('index', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
                'id_menu' => $id_menu,
                'name' => $name,
            ]);
        }
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single MenuItem model.
     * @param int $id_item Item ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        if (!\Yii::$app->user->can('menuBackendItemView')) {
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
    public function actionCreate($id_menu)
    {
        if (!\Yii::$app->user->can('menuBackendItemCreate')) {
            throw new \yii\web\ForbiddenHttpException(Module::t('You are not allowed to access this page.'));
        }
        $model = new MenuItem();
        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {
                $model->id_menu = $id_menu;
                if($model->save())
                    return $this->redirect(['index', 'id_menu' => $model->id_menu]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
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
        if (!\Yii::$app->user->can('menuBackendItemUpdate')) {
            throw new \yii\web\ForbiddenHttpException(Module::t('You are not allowed to access this page.'));
        }
        $model = $this->findModel($id);
        
        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['index', 'id_menu' => $model->id_menu]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing MenuItem model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id_item Item ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        if (!\Yii::$app->user->can('menuBackendItemDelete')) {
            throw new \yii\web\ForbiddenHttpException(Module::t('You are not allowed to access this page.'));
        }
        $model = $this->findModel($id);
        $this->findModel($id)->delete();
        return $this->redirect(['index', 'id_menu' => $model->id_menu]);
    }

    public function actionRouteType() {
        if (!\Yii::$app->user->can('menuBackendItemRouteType')) {
            throw new \yii\web\ForbiddenHttpException(Module::t('You are not allowed to access this page.'));
        }
        $out = [];
        if($this->request->isPost){
            $request = $this->request->post('depdrop_parents');
            $moduleName = $request[0];
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
        if (!\Yii::$app->user->can('menuBackendItemRoute')) {
            throw new \yii\web\ForbiddenHttpException(Module::t('You are not allowed to access this page.'));
        }
        $out = [];
        if($this->request->isPost){
            $request = $this->request->post('depdrop_parents');
            $moduleName = $request[0];
            $module = Yii::$app->getModule($moduleName);
            $menuItems = $module->getMenuItems();
            $routeType = $request[1];
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
                    }
                }
            }
            return json_encode(['output' => $out, 'selected' => '']);
        }
    }

    public function actionModel() {
        if (!\Yii::$app->user->can('menuBackendItemModel')) {
            throw new \yii\web\ForbiddenHttpException(Module::t('You are not allowed to access this page.'));
        }
        $out = [];
        if($this->request->isPost){
            $request = $this->request->post('depdrop_parents');
            $moduleName = $request[0];
            $routeType = $request[1];
            $route = $request[2];
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
            $data = $modelName::find()->select(['id' => $field['id'], 'name' => $field['name']])->asArray()->all();
            
            return json_encode(['output' => $data, 'selected' => '']);
        }
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
