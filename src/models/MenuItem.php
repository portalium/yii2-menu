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
 * @property int $id_parent
 * @property int $id_menu
 * @property string $date_create
 * @property string $date_update
 */
class MenuItem extends \yii\db\ActiveRecord
{
    public $module;
    const TYPE = [
        'root' => '1',
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
            [['label', 'slug', 'icon', 'id_menu', 'type'], 'required'],
            [['type', 'id_parent', 'id_menu', 'sort'], 'integer'],
            [['data'], 'string'],
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
            'id_parent' => Module::t('Parent ID'),
            'id_menu' => Module::t('Menu ID'),
            'date_create' => Module::t('Date Created'),
            'date_update' => Module::t('Date Updated'),
        ];
    }

    public static function getTypeList()
    {
        return [
            'root' => Module::t('Root'),
            'module' => Module::t('Module'),
            'url' => Module::t('Url'),
        ];
    }

    public static function getTypes()
    {
        return [
            '1' => 'Root',
            '2' => 'Module',
            '3' => 'Url',
        ];
    }

    public static function getModuleList(){
        //yii app all modules
        $modules = Yii::$app->getModules();
        $list = [];
        foreach ($modules as $key => $value) {
            $list[$key] = $key;
        }
        return $list;
    }
}
