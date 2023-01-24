<?php

namespace portalium\menu\models;

use Yii;
use portalium\menu\Module;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%menu_item}}".
 *
 * @property int $id_item
 * @property string $label
 * @property string $slug
 * @property int $type
 * @property string $style
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
    public $menuRoute;
    public $menuType;
    public $icon;
    public $color;
    public $iconSize;

    const TYPE = [
        'route' => '1',
        'module' => '2',
        'url' => '3',
    ];

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'date_create',
                'updatedAtAttribute' => 'date_update',
                'value' => date("Y-m-d H:i:s"),
            ],
            [
                'class' => 'yii\behaviors\BlameableBehavior',
                'createdByAttribute' => 'id_user',
                'updatedByAttribute' => 'id_user',
                'value' => isset(Yii::$app->user) ? Yii::$app->user->id : 0,
            ],
        ];
    }

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
            [['label', 'slug', 'style', 'id_menu', 'type'], 'required'],
            [['type', 'id_parent', 'id_menu', 'sort', 'id_user'], 'integer'],
            [['data', 'module', 'routeType', 'route', 'model', 'url', 'name_auth', 'menuType'], 'string'],
            [['date_create', 'date_update', 'menuRoute', 'icon', 'color', 'iconSize'], 'safe'],
            [['label', 'slug', 'style'], 'string', 'max' => 255],
            [['style'], 'default', 'value' => '{"icon":"0xf0f6","color":"rgb(234, 153, 153)","iconSize":"24"}'],
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
            'style' => Module::t('Style'),
            'data' => Module::t('Data'),
            'type' => Module::t('Type'),
            'sort' => Module::t('Sort'),
            'module' => Module::t('Module'),
            'routeType' => Module::t('Route Type'),
            'route' => Module::t('Route'),
            'model' => Module::t('Model'),
            'url' => Module::t('Url'),
            'name_auth' => Module::t('Name Auth'),
            'id_parent' => Module::t('Parent'),
            'id_menu' => Module::t('Menu ID'),
            'id_user' => Module::t('User ID'),
            'date_create' => Module::t('Date Created'),
            'date_update' => Module::t('Date Updated'),
            'menuRoute' => Module::t('Menu Route'),
            'menuType' => Module::t('Menu Type'),
            'icon' => Module::t('Icon'),
            'color' => Module::t('Color'),
            'iconSize' => Module::t('Icon Size'),
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

    public static function getModuleList()
    {
        //yii app all modules
        $modules = Yii::$app->getModules();
        $list = [];
        foreach ($modules as $key => $module) {
            if (isset($module->menuItems)) {
                $list[$key] = (isset($module::$description)) ? $module->t($module::$description) : $key;
            }
        }
        return $list;
    }

    public static function getParents($id_menu)
    {
        $parents = self::find()->where(['id_parent' => 0, 'id_menu' => $id_menu])->all();
        $list = [];
        $list['0'] = Module::t('Root Menu');
        foreach ($parents as $parent) {
            $list[$parent->id_item] = isset($parent->module) ? Yii::$app->getModule($parent->module)->t($parent->label) : Module::t($parent->label);
        }
        return $list;
    }

    public function hasChildren()
    {
        return self::find()->where(['id_parent' => $this->id_item])->count() > 0;
    }

    //get children of menu item
    public static function getMenuTree($id_item)
    {
        //order by sort ASC
        $children = self::find()->where(['id_parent' => $id_item])->orderBy('sort ASC')->all();
        $list = [];
        /*
         * title, id, hasChildren, children
         */
        foreach ($children as $child) {
            if ($child->hasChildren()) {
                $list[$child->id_item] = [
                    'title' => isset($child->module) ? Yii::$app->getModule($child->module)->t($child->label) : Module::t($child->label),
                    'id' => $child->id_item,
                    'sort' => $child->sort,
                    'hasChildren' => true,
                    'children' => self::getMenuTree($child->id_item),
                ];
            } else {
                $list[$child->id_item] = [
                    'title' => isset($child->module) ? Yii::$app->getModule($child->module)->t($child->label) : Module::t($child->label),
                    'id' => $child->id_item,
                    'sort' => $child->sort,
                    'hasChildren' => false,
                ];
            }
        }
        return $list;
    }

    public static function getAuthList()
    {
        $auth = Yii::$app->authManager;
        $list = [];
        //add divider disable
        $list['role'] = 'Role';
        foreach ($auth->getRoles() as $key => $role) {
            $list[$key] = $role->description;
        }
        $list['guest'] = 'Guest';
        $list['permission'] = 'Permission';
        foreach ($auth->getPermissions() as $key => $permission) {
            $list[$key] = $permission->description;
        }

        

        return $list;
    }

    public function beforeSave($insert)
    {
        if ($this->type == self::TYPE['module']) {
            $json_data['data'] = [
                'module' => $this->module,
                'routeType' => $this->routeType,
                'route' => $this->route,
                'model' => $this->model,
                'menuRoute' => $this->menuRoute,
                'menuType' => $this->menuType,
            ];
        } elseif ($this->type == self::TYPE['url']) {
            $json_data['data'] = [
                'url' => $this->url,
            ];
        } elseif ($this->type == self::TYPE['route']) {
            $json_data['data'] = [
                'route' => $this->url,
                'module' => $this->module,
            ];
        }
        $this->data = json_encode($json_data);

        $json_style['icon'] = $this->icon;
        $json_style['color'] = $this->color;
        $json_style['iconSize'] = $this->iconSize;
        $this->style = json_encode($json_style);

        return parent::beforeSave($insert);
    }

    public function afterFind()
    {
        $json_data = json_decode($this->data, true);
        if ($this->type == self::TYPE['module']) {
            $this->module = $json_data['data']['module'];
            $this->routeType = $json_data['data']['routeType'];
            $this->route = $json_data['data']['route'];
            $this->model = $json_data['data']['model'];
            $this->menuRoute = $json_data['data']['menuRoute'];
            $this->menuType = $json_data['data']['menuType'];
        } elseif ($this->type == self::TYPE['url']) {
            $this->url = $json_data['data']['url'];
        } elseif ($this->type == self::TYPE['route']) {
            $this->url = $json_data['data']['route'];
        }
        $json_style = json_decode($this->style, true);
        $this->icon = $json_style['icon'];
        $this->color = $json_style['color'];
        $this->iconSize = $json_style['iconSize'];

        return parent::afterFind();
    }

    public static function sort($data)
    {
        $data = json_decode($data['data'], true);
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
                self::sortChildren($item['children'],$item['id'], $index);
            }
        }
        return "success";
    }

    public static function sortChildren($children, $id_parent, &$index)
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
                self::sortChildren($child['children'], $child['id'], $index);
            }

        }
    }
}
