<?php

namespace portalium\menu\models;

use portalium\menu\Module;
use Yii;

/**
 * This is the model class for table "{{%menu_item_child}}".
 *
 * @property int $id_item
 * @property int $id_child
 *
 * @property MenuItem $child
 * @property MenuItem $item
 */
class ItemChild extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%' . Module::$tablePrefix . 'item_child}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_item', 'id_child'], 'required'],
            [['id_item', 'id_child'], 'integer'],
            [['id_child'], 'exist', 'skipOnError' => true, 'targetClass' => MenuItem::class, 'targetAttribute' => ['id_child' => 'id_item']],
            [['id_item'], 'exist', 'skipOnError' => true, 'targetClass' => MenuItem::class, 'targetAttribute' => ['id_item' => 'id_item']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_item' => Module::t('Id Item'),
            'id_child' => Module::t('Id Child'),
        ];
    }

    /**
     * Gets query for [[Child]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getChild()
    {
        return $this->hasOne(MenuItem::class, ['id_item' => 'id_child']);
    }

    /**
     * Gets query for [[Item]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getItem()
    {
        return $this->hasOne(MenuItem::class, ['id_item' => 'id_item']);
    }
    
}
