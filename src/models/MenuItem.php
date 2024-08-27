<?php

namespace portalium\menu\models;

use Yii;
use portalium\menu\Module;
use portalium\menu\Module\Yii2;
use portalium\menu\models\ItemChild;
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
 * @property int $parent
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
    public $display;
    public $childDisplay;
    public $placement;
    public $color;
    public $iconSize;
    public $id_parent;

    const TYPE = [
        'route' => '1',
        'module' => '2',
        'url' => '3',
    ];

    const TYPE_DISPLAY = [
        'icon' => '1',
        'text' => '2',
        'icon-text' => '3',
    ];

    const LABEL_PLACEMENT = [
        'side-by-side' => '1',
        'top-to-bottom' => '2',
        'default' =>'3',
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
            [['label', 'slug', 'style', 'id_menu', 'type','url'], 'required'],
            [['type', 'id_menu', 'sort', 'id_user',], 'integer'],
            [['data', 'module', 'routeType', 'route', 'model', 'url', 'name_auth', 'menuType'], 'string'],
            [['date_create', 'date_update', 'parent', 'menuRoute', 'icon', 'color', 'iconSize', 'display', 'childDisplay', 'placement',], 'safe'],
            [['label', 'slug', 'style'], 'string', 'max' => 255],
            //[['style'], 'default', 'value' => '{"icon":"0xf0f6","color":"rgb(234, 153, 153)","iconSize":"24","display":,'.self::TYPE_DISPLAY['icon-text'].'","childDisplay":","'.self::TYPE_DISPLAY['icon-text'].'"}'],
            [['style'], 'default', 'value' => '{"icon":"0xf0f6","color":"rgb(234, 153, 153)","iconSize":"24","display":'.self::TYPE_DISPLAY['icon-text'].',"childDisplay":'.self::TYPE_DISPLAY['icon-text'].',"placement":'.self::LABEL_PLACEMENT['default'].'}']
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
            'name_auth' => Module::t('Access'),
            'parent' => Module::t('Parent'),
            'id_menu' => Module::t('Menu ID'),
            'id_user' => Module::t('User ID'),
            'date_create' => Module::t('Date Created'),
            'date_update' => Module::t('Date Updated'),
            'menuRoute' => Module::t('Menu Route'),
            'menuType' => Module::t('Menu Type'),
            'icon' => Module::t('Icon'),
            'color' => Module::t('Color'),
            'iconSize' => Module::t('Icon Size'),
            'display' => Module::t('Display'),
            'placement' => Module::t('Placement'),
            'child_display' => Module::t('Child Display'),
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
    public static function getDisplays()
    {

        return [
            '1' => 'icon',
            '2' => 'text',
            '3' => 'icon-text',
        ];
    }
    
    public static function getPlacements()
    {

        return [
            '1' => 'side-by-side',
            '2' => 'top-to-bottom',
            '3' => 'default',
        ];
    }
    public static function getDisplayList()
    {
        return [
            self::TYPE_DISPLAY['icon'] => Module::t('Only Icon'),
            self::TYPE_DISPLAY['text'] => Module::t('Only Text'),
            self::TYPE_DISPLAY['icon-text'] => Module::t('Icon and Text')
            ];
    }

    public static function getPlacementList()
    {
        return [
            self::LABEL_PLACEMENT['side-by-side'] => Module::t('Side By Side'),
            self::LABEL_PLACEMENT['top-to-bottom'] => Module::t('Top To Bottom'),
            self::LABEL_PLACEMENT['default'] => Module::t('Default'),
            ];
    }

    public static function getModuleList()
    {
        //yii app all modules
        $modules = Yii::$app->getModules();
        $list = [];
        foreach ($modules as $key => $module) {
            if (isset($module->menuItems)) {
                $list[$key] = (isset($module::$name)) ? $module->t($module::$name) : $key;
            }
        }
        return $list;
    }

    public function getParent()
    {
        return $this->hasOne(ItemChild::class, ['id_child' => 'id_item']);
    }

    public function getChildren()
    {
        return $this->hasMany(ItemChild::class, ['id_item' => 'id_item']);
    }

    public function hasChildren()
    {
        return $this->getChildren()->count() > 0;
    }

    public function getMenu()
    {
        return $this->hasOne(Menu::class, ['id_menu' => 'id_menu']);
    }

    public static function getParents($id_menu)
    {
        $parents = self::find()->where(['id_menu' => $id_menu])->all();
        $list = [];
        $list['0'] = Module::t('Root Menu');
        foreach ($parents as $parent) {
            //if(!isset($parent->parent))
                $list[$parent->id_item] = isset($parent->module) ? Yii::$app->getModule($parent->module)->t($parent->label) : Module::t($parent->label);
        }
        
        return $list;
    }

    //get children of menu item
    public static function getMenuTree($id_item)
    {
        $model = self::findOne($id_item);
        $children = $model->children;

        $list = [];

        foreach ($children as $child) {
            try {
                if ($child->child->hasChildren()) {
                    $list[$child->child->id_item] = [
                        'title' => isset($child->child->module) ? Yii::$app->getModule($child->child->module)->t($child->child->label) : Module::t($child->child->label),
                        'id' => $child->child->id_item,
                        'sort' => $child->child->sort,
                        'hasChildren' => true,
                        'children' => self::getMenuTree($child->id_child),
                    ];
                } else {
                    $list[$child->child->id_item] = [
                        'title' => isset($child->child->module) ? Yii::$app->getModule($child->child->module)->t($child->child->label) : Module::t($child->child->label),
                        'id' => $child->child->id_item,
                        'sort' => $child->child->sort,
                        'hasChildren' => false,
                    ];
                }
            } catch (\Throwable $th) {
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
        $json_style['display'] = $this->display;
        $json_style['childDisplay'] = $this->childDisplay;
        $json_style['placement'] = $this->placement;
        $this->style = json_encode($json_style);
        $this->id_user = 1;
        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes)
    {
        if ($this->id_parent != 0 && $this->id_parent != null) {
            $parent = ItemChild::findOne(['id_item' => $this->id_parent, 'id_child' => $this->id_item]);
            if ($parent == null) {
                $parent = new ItemChild();
                $parent->id_item = $this->id_parent;
                $parent->id_child = $this->id_item;
                $parent->save();
            }
        }
        return parent::afterSave($insert, $changedAttributes);
    }

    public function afterFind()
    {
        
        $this->loadData();
        return parent::afterFind();
    }

    public function loadData()
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
        $this->display = isset($json_style['display']) ? $json_style['display'] : self::TYPE_DISPLAY['icon-text'];
        $this->childDisplay = isset($json_style['childDisplay']) ? $json_style['childDisplay'] : false;
        $this->placement = isset($json_style['placement']) ? $json_style['placement'] : self::LABEL_PLACEMENT['default'];
        $this->id_parent = isset($this->getParent()->one()->id_item) ? $this->getParent()->one()->id_item : 0;
    }

    public static function sort($data)
    {
        
        $data = json_decode($data['data'], true);

        foreach ($data as $item) {
            $model = MenuItem::findOne($item['id']);
            if ($model) {
                ItemChild::deleteAll(['id_item' => $item['id']]);
            }
            self::removeChildrenRecursive($item);
        }
        
        //[{"id":1},{"id":2,"children":[{"id":4}]},{"id":8},{"id":9},{"id":3},{"id":5},{"id":6},{"id":7}]
        $index = 0;
        foreach ($data as $item) {
            $model = MenuItem::findOne($item['id']);
            if (!$model) {
                continue;
            }
            $model->sort = $index;
            $model->id_user = 1;
            !$model->save();
            
            $index++;
            
            if (isset($item['children'])) {
                $index = self::sortChildren($item['children'],$item['id'], $index);
            }else{
                ItemChild::deleteAll(['id_item' => $item['id']]);
            }
        }
        return "success";
    }

    public static function removeChildrenRecursive($item)
    {

        if (isset($item['children'])) {
            foreach ($item['children'] as $child) {
                ItemChild::deleteAll(['id_item' => $item['id']]);
                self::removeChildrenRecursive($child);
            }
        }else{
            ItemChild::deleteAll(['id_item' => $item['id']]);
        }
    }

    public static function sortChildren($children, $parent, $index)
    {
        foreach ($children as $child) {
            $model = MenuItem::findOne($child['id']);
            if (!$model) {
                continue;
            }
            $model->sort = $index;
            $model->id_user = 1;
            $itemChild = new ItemChild();
            $itemChild->id_item = $parent;
            $itemChild->id_child = $child['id'];
            if (!ItemChild::find()->where(['id_item' => $parent, 'id_child' => $child['id']])->one()) {
                $itemChild->save();
            }else{
                $itemChild = ItemChild::find()->where(['id_item' => $parent, 'id_child' => $child['id']])->one();
            }
            
            !$model->save();
            
            $index++;
            if (isset($child['children'])) {
                $index = self::sortChildren($child['children'], $child['id'], $index);
            }else{
                ItemChild::deleteAll(['id_item' => $child['id']]);
            }
        }
        return $index;
    }

    public function addItem($id_item, $addChildren = false){
        $item = MenuItem::findOne($id_item);
        
        $copyItem = new MenuItem();
        $copyItem->attributes = $item->attributes;
        $copyItem->id_item = null;
        $copyItem->id_user = Yii::$app->user->id;
        $copyItem->id_menu = $this->id_menu;
        $copyItem->loadData();
        $copyItem->save();
        
        $itemChild = new ItemChild();
        $itemChild->id_item = $this->id_item;
        $itemChild->id_child = $copyItem->id_item;
        if(!ItemChild::find()->where(['id_item' => $this->id_item, 'id_child' => $copyItem->id_item])->one())
            $itemChild->save();
        else
            $itemChild = ItemChild::find()->where(['id_item' => $this->id_item, 'id_child' => $copyItem->id_item])->one();

        if($addChildren){
            $children = ItemChild::find()->where(['id_item' => $id_item])->all();
            foreach ($children as $child) {
                $copyItem->addItem($child->id_child, $addChildren);
            }
        }        
    }

    // 
    public function getChildrenArray($id_menu)
    {
        $items = MenuItem::find()->where(['id_menu' => $id_menu])->orderBy('sort')->all();
        $arr = [];
        foreach ($items as $item) {
            if (!isset($item->parent->id_item) || $item->parent->id_item == null) {
                $arr[] = [
                    'id' => $item->id_item,
                ];
            }else{
                $added = false;
                foreach ($arr as $key => $value) {
                    if($value['id'] == $item->parent->id_item){
                        $arr[$key]['children'][] = [
                            'id' => $item->id_item,
                        ];
                        $added = true;
                    }
                }
                if(!$added){
                    $arr[] = [
                        'id' => $item->parent->id_item,
                        'children' => [
                            [
                                'id' => $item->id_item,
                            ]
                        ]
                    ];
                }
            }
        }
        return $arr;
    }

    public function deleteChildren()
    {
        $children = ItemChild::find()->where(['id_item' => $this->id_item])->all();
        foreach ($children as $child) {
            $model = MenuItem::findOne($child->id_child);
            if ($model) {
                $model->deleteChildren();
                $model->delete();
            }
        }
    }

    public function beforeDelete()
    {
        ItemChild::deleteAll(['id_item' => $this->id_item]);
        ItemChild::deleteAll(['id_child' => $this->id_item]);
        return parent::beforeDelete();
    }
}
