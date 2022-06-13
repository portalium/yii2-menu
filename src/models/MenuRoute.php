<?php

namespace portalium\menu\models;

use Yii;
use portalium\menu\Module;
/**
 * This is the model class for table "{{%menu_menu_route}}".
 * @property int $id_menu_route
 * @property string $title
 * @property string $route
 * @property int $type
 * @property int $module
 * @property string $date_create
 * @property string $date_update
 */
class MenuRoute extends \yii\db\ActiveRecord
{
    const TYPE = [
        'web' => '1',
        'mobile' => '2'
    ];
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%' . Module::$tablePrefix . 'menu_route}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'route', 'type','module'], 'required'],
            [['type'], 'integer'],
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
            'id_menu_route' => Module::t('ID Menu Route'),
            'title' => Module::t('Title'),
            'route' => Module::t('Route'),
            'type' => Module::t('Type'),
            'module' => Module::t('Module'),
            'date_create' => Module::t('Date Create'),
            'date_update' => Module::t('Date Update'),
        ];
    }

    public static function getTypes()
    {
        return [
            '1' => Module::t('Web'),
            '2' => Module::t('Mobile')
        ];
    }

}
