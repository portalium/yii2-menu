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
 * @property string $url
 * @property string $icon
 * @property int $id_parent
 * @property int $id_menu
 * @property string $date_create
 * @property string $date_update
 */
class MenuItem extends \yii\db\ActiveRecord
{
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
            [['label', 'slug', 'url', 'icon', 'id_menu'], 'required'],
            [['url'], 'string'],
            [['id_parent', 'id_menu'], 'integer'],
            [['date_create', 'date_update'], 'safe'],
            [['label', 'slug'], 'string', 'max' => 255],
            [['icon'], 'string', 'max' => 64],
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
            'url' => Module::t('Url'),
            'icon' => Module::t('Icon'),
            'id_parent' => Module::t('Parent ID'),
            'id_menu' => Module::t('Menu ID'),
            'date_create' => Module::t('Date Created'),
            'date_update' => Module::t('Date Updated'),
        ];
    }
}
