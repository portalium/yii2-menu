<?php

namespace portalium\menu\models;

use portalium\menu\Module;
use Yii;

/**
 * This is the model class for table "{{%menu_item}}".
 *
 * @property int $id_item
 * @property string $label
 * @property string $slug
 * @property int $type
 * @property string $icon
 * @property string $data
 * @property int $sort
 * @property int $name_auth
 * @property int $id_parent
 * @property int $id_menu
 * @property string $date_create
 * @property string $date_update
 */
class MenuItem extends \yii\db\ActiveRecord
{
    public $module;
    public $routeType;
    public $route;
    public $model;
    public $url;
    const TYPE = [
        'route' => '1',
        'module' => '2',
        'url' => '3',
    ];
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%' . Module::$tablePrefix . 'item}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['label', 'slug', 'id_menu', 'type'], 'required'],
            [['type', 'id_parent', 'id_menu', 'sort'], 'integer'],
            [['data', 'module', 'routeType', 'route', 'model', 'url', 'name_auth'], 'string'],
            [['date_create', 'date_update'], 'safe'],
            [['label', 'slug', 'icon'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_item' => Module::t('Item ID'),
            'label' => Module::t('Label'),
            'slug' => Module::t('Slug'),
            'icon' => Module::t('Icon'),
            'data' => Module::t('Data'),
            'sort' => Module::t('Sort'),
            'module' => Module::t('Module'),
            'routeType' => Module::t('Route Type'),
            'route' => Module::t('Route'),
            'model' => Module::t('Model'),
            'url' => Module::t('Url'),
            'name_auth' => Module::t('Name Auth'),
            'id_parent' => Module::t('Parent ID'),
            'id_menu' => Module::t('Menu ID'),
            'date_create' => Module::t('Date Created'),
            'date_update' => Module::t('Date Updated'),
        ];
    }

    public static function getTypeList()
    {
        return [
            'route' => Module::t('Route'),
            'module' => Module::t('Module'),
            'url' => Module::t('Url'),
        ];
    }

    public static function getTypes()
    {
        return [
            '1' => 'Route',
            '2' => 'Module',
            '3' => 'Url',
        ];
    }

    public static function getModuleList(){
        //yii app all modules
        $modules = Yii::$app->getModules();
        $list = [];
        foreach ($modules as $key => $module) {
            if(isset($module->menuItems)){
                $list[$key] = (isset($module::$description)) ? $module->t($module::$description) : $key;
            }
            
        }
        return $list;
    }

    public static function getParents($id_menu){
        $parents = self::find()->where(['id_parent' => 0, 'id_menu' => $id_menu])->all();
        $list = [];
        $list['0'] = Module::t('Root Menu');
        foreach ($parents as $parent) {
            $list[$parent->id_item] = $parent->label;
        }
        return $list;
    }

    public static function getAuthList(){
        $auth = Yii::$app->authManager;
        $list = [];
        //add divider disable
        $list['role'] = 'Role';
        foreach ($auth->getRoles() as $key => $role) {
            $list[$key] = $role->description;
        }
        $list['permission'] = 'Permission';
        foreach ($auth->getPermissions() as $key => $permission) {
            $list[$key] = $permission->description;
        }

        return $list;
    }

    public function beforeSave($insert)
    {
        $json_data['type'] = $this->type;
        if($this->type == self::TYPE['module']){
            $json_data['data'] = [
                'module' => $this->module,
                'routeType' => $this->routeType,
                'route' => $this->route,
                'model' => $this->model,
            ];
        }elseif($this->type == self::TYPE['url']){
            $json_data['data'] = [
                'url' => $this->url,
            ];
        }elseif($this->type == self::TYPE['route']){
            $json_data['data'] = [
                'route' => $this->url,
            ];
        }
        $this->data = json_encode($json_data);
        $this->sort = MenuItem::find()->where(['id_menu' => $this->id_menu])->max('sort') + 1;
        return parent::beforeSave($insert);
    }

    public function afterFind()
    {
        $json_data = json_decode($this->data, true);
        $this->type = $json_data['type'];
        if($this->type == self::TYPE['module']){
            $this->module = $json_data['data']['module'];
            $this->routeType = $json_data['data']['routeType'];
            $this->route = ($this->routeType == 'widget') ? str_replace('\\', '\\\\', $json_data['data']['route']) : $json_data['data']['route'];
            $this->model = $json_data['data']['model'];
        }elseif($this->type == self::TYPE['url']){
            $this->url = $json_data['data']['url'];
        }elseif($this->type == self::TYPE['route']){
            $this->url = $json_data['data']['route'];
        }
        parent::afterFind();
    }
}
