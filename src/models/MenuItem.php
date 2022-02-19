<?php

namespace portalium\menu\models;

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
 * @property string $date_created
 * @property string $date_update
 */
class MenuItem extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%menu_item}}';
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
            [['date_created', 'date_update'], 'safe'],
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
            'id_item' => 'Item ID',
            'label' => 'Label',
            'slug' => 'Slug',
            'url' => 'Url',
            'icon' => 'Icon',
            'id_parent' => 'Parent ID',
            'id_menu' => 'Menu ID',
            'date_created' => 'Date Created',
            'date_update' => 'Date Updated',
        ];
    }
}
