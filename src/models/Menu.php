<?php

namespace portalium\menu\models;

use Yii;
use portalium\menu\Module;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%menu}}".
 *
 * @property int $id_menu
 * @property string $name
 * @property string $slug
 * @property int $type
 * @property int $direction
 * @property string $date_create
 * @property string $date_update
 */
class Menu extends \yii\db\ActiveRecord
{
    const TYPE = [
        'web' => '1',
        'mobile' => '2'
    ];

    const DIRECTION = [
        'vertical' => '1',
        'horizontal' => '2'
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
        return '{{%' . Module::$tablePrefix . 'menu}}';
    }

    public function extraFields()
    {
        return ['items'];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'slug', 'type', 'direction'], 'required'],
            [['type', 'id_user', 'direction'], 'integer'],
            [['date_create', 'date_update'], 'safe'],
            [['name', 'slug'], 'string', 'max' => 255]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_menu' => Module::t('Menu ID'),
            'name' => Module::t('Name'),
            'slug' => Module::t('Slug'),
            'type' => Module::t('Type'),
            'direction' => Module::t('Direction'),
            'id_user' => Module::t('User ID'),
            'date_create' => Module::t('Date Created'),
            'date_update' => Module::t('Date Updated'),
        ];
    }

    public static function getTypes()
    {
        return [
            self::TYPE['web'] => Module::t('Web'),
            self::TYPE['mobile'] => Module::t('Mobile')
        ];
    }

    public static function getDirections()
    {
        return [
            self::DIRECTION['vertical'] => Module::t('Vertical'),
            self::DIRECTION['horizontal'] => Module::t('Horizontal')
        ];
    }

    public static function getDirection($direction)
    {
        $directions = [self::DIRECTION['vertical'] => "vertical", self::DIRECTION['horizontal'] => "horizontal"];
        return $directions[$direction];
    }

    public function getItems()
    {
        return $this->hasMany(MenuItem::class, ['id_menu' => 'id_menu'])->orderBy('sort');
    }

   
    public static function getMenuWithChildren($id_menu)
    {
        // EN: 1. Fetch all items in a single query (Eager Loading) to prevent N+1 issue.
        $items = MenuItem::find()->where(['id_menu' => $id_menu])->orderBy('sort')->all();

        if (empty($items)) {
            return [];
        }

        $itemIds = [];
        foreach ($items as $item) {
            $itemIds[] = $item->id_item;
        }

        // Fetch all parent-child relationships in a single query.
        $relations = ItemChild::find()->where(['id_child' => $itemIds])->asArray()->all();

        // EN: 3. Map relations for O(1) memory access.
        $childToParent = [];
        $parentToChildren = [];
        foreach ($relations as $rel) {
            $childToParent[$rel['id_child']] = $rel['id_item'];
            $parentToChildren[$rel['id_item']][] = $rel['id_child'];
        }

        $itemsById = [];
        foreach ($items as $item) {
            
           
            $title = Module::t($item->label);
            if (!empty($item->module)) {
                $title = Yii::t($item->module, $item->label);
            }

            $itemsById[$item->id_item] = [
                'title' => $title,
                'id' => $item->id_item,
                'hasChildren' => isset($parentToChildren[$item->id_item]),
                'sort' => $item->sort,
                'children' => [] 
            ];
        }

        //Build the hierarchical tree in RAM using PHP references (&$node) for maximum speed.
        $rootItems = [];
        foreach ($itemsById as $id => &$node) {
            if (isset($childToParent[$id])) {
                $parentId = $childToParent[$id];
                //If parent exists, add this node to parent's children array
                if (isset($itemsById[$parentId])) {
                    $itemsById[$parentId]['children'][] = &$node;
                } else {
                    $rootItems[] = &$node; 
                }
            } else {
                //If no parent, it's a root menu item
                $rootItems[] = &$node;
            }
        }

        return self::jsonSortWithSortRecursive($rootItems);
    }
    
    public static function jsonSortWithSortRecursive($data)
    {
        foreach ($data as $key => $value) {
            if (isset($value['children']) && is_array($value['children'])) {
                $data[$key]['children'] = self::jsonSortWithSortRecursive($value['children']);
            }
        }
        usort($data, function ($a, $b) {
            return $a['sort'] <=> $b['sort'];
        });
        return $data;
    }

    public function addItem($id_item, $addChildren = false, $id_parent = null)
    {
        try {
            $item = MenuItem::findOne($id_item);
            $copyItem = new MenuItem();
            $copyItem->attributes = $item->attributes;
            $copyItem->id_item = null;
            $copyItem->id_menu = $this->id_menu;
            $copyItem->data = $item->data;
            $copyItem->loadData();
            $copyItem->save();
            if ($addChildren) {
                foreach ($item->children as $child) {
                    $copyItem->addItem($child->id_child, $addChildren);
                }
            }
            if ($id_parent != null || $id_parent != 0) {
                $itemChild = new ItemChild();
                $itemChild->id_item = $id_parent;
                $itemChild->id_child = $copyItem->id_item;
                $itemChild->save();
            }
        } catch (\Exception $e) {
            return false;
        }

        return $copyItem;
    }
}