<?php

namespace portalium\menu\controllers\backend;

use Yii;
use ReflectionClass;
use portalium\menu\Module;
use yii\filters\VerbFilter;
use portalium\web\Controller;
use portalium\menu\models\Menu;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use portalium\menu\models\MenuItem;
use portalium\user\models\UserSearch;
use portalium\menu\models\MenuItemSearch;
use portalium\user\models\User;

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
        $model = $this->findModel($id);
        $this->findModel($id)->delete();
        return $this->redirect(['index', 'id_menu' => $model->id_menu]);
    }

    public function actionController() {
        $out = [];
        if($this->request->isPost){
            $moduleName = $this->request->post('depdrop_parents');
            if ($moduleName != null) {
                $moduleName = $moduleName[0];
                $module = Yii::$app->getModule($moduleName);
                $modulePath = $module->getBasePath();
                $folderpath = $modulePath.'/controllers/backend/';
                $files = scandir($folderpath);
                $controllers = [];
                foreach ($files as $file) {
                    if (strpos($file, 'Controller.php') !== false) {
                        $controllerName = str_replace('Controller.php', '', $file);
                        $controllers[] = $controllerName;
                    }
                }
                foreach ($controllers as $controller) {
                    $out[] = ['id' => $controller, 'name' => $controller];
                }
                return json_encode(['output'=>$out, 'selected'=>'']);
            }
        }
        return json_encode(['output'=>'', 'selected'=>'']);
    }

    public function actionModel() {
        $out = [];
        if($this->request->isPost){
            $moduleName = $this->request->post('depdrop_parents');
            if ($moduleName != null) {
                $moduleName = $moduleName[0];
                $module = Yii::$app->getModule($moduleName);
                $modulePath = $module->getBasePath();
                $folderpath = $modulePath.'/models/';
                $files = scandir($folderpath);
                $models = [];
                foreach ($files as $file) {
                    if (strpos($file, '.php') !== false && strpos($file, 'Search') === false && strpos($file, 'Form') === false) {
                        $modelName = str_replace('.php', '', $file);
                        $models[] = $modelName;
                    }
                }
                foreach ($models as $model) {
                    $moduleClass = Yii::$app->getModule($moduleName);
                    $moduleClassNamespace = (new ReflectionClass($moduleClass))->getNamespaceName();
                    //model first letter uppercase
                    $modelPath = $moduleClassNamespace.'\\models\\'.ucfirst($model);
                    
                    $out[] = ['id' => $modelPath, 'name' => $model];
                }
                return json_encode(['output'=>$out, 'selected'=>'']);
            }
        }
        return json_encode(['output'=>'', 'selected'=>'']);
    }

    public function actionData(){
        $out = [];
        if($this->request->isPost){
            $modelName = $this->request->post('depdrop_parents');
            if ($modelName != null) {
                $modelName = $modelName[0];
                Yii::error($modelName);
                $model = new $modelName;
                $modelClassNamespace = (new ReflectionClass($model))->getNamespaceName();
                $modelPath = $modelName;
                $model = new $modelPath;
                $dataProvider = new ActiveDataProvider([
                    'query' => $model->find(),
                ]);
                $models = $dataProvider->getModels();
                $attributes = $model->attributes();
                
                foreach ($models as $model) {
                    $out[] = ['id' => implode(',', $model->primaryKey()), 'name' => $attributes[1]];
                }
                
                return json_encode(['output'=>$out, 'selected'=>'']);
            }
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
