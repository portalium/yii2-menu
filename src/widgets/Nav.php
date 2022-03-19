<?php

namespace portalium\menu\widgets;

use Yii;
use yii\base\Widget;
use yii\helpers\Html;
use portalium\menu\Module;
use portalium\menu\models\MenuItem;
use portalium\theme\widgets\NavBar;
use portalium\theme\widgets\Nav as BaseNav;

class Nav extends Widget
{
    public $model;
    public $navbar;
    public function init()
    {
        parent::init();
        if ($this->model === null) {
            throw new \yii\base\InvalidConfigException('MenuWidget::$model must be set.');
        }

    }

    public function run()
    {

        $items = [];
        foreach ($this->model->items as $item) {
            if ($item->id_parent == 0){
                $url = $this->getUrl($item);
                $data = json_decode($item->data, true);
                $items[] = 
                    ($data["data"]["routeType"] == "widget") ? $data["data"]["route"] : 
                        [
                            'label' => $item->label,
                            'url' => $url,
                            'items' => $this->getChildItems($item->id_item),
                            'visible' => Yii::$app->user->can($item->name_auth),
                        ];

            }
        }

        echo BaseNav::widget([
            'options' => ['class' => 'navbar-nav navbar-right'],
            'items' => $items,
        ]);
        
    }
    
    public function getChildItems($id_parent)
    {
        $items = [];
        foreach ($this->model->items as $item) {
            if ($item->id_parent == $id_parent) {
                     
                $url = $this->getUrl($item);
                $itemTemp = [
                    'label' => $item->label,
                    'url' => $url,
                    'visible' => Yii::$app->user->can($item->name_auth),
                ];
                $list = $this->getChildItems($item->id_item);
                if(!empty($list)){
                    $itemTemp['items'] = $list;
                }
                $items[] = $itemTemp;
            }
        }
        
        return $items;

    }
    
    public function getUrl($item){
        $url = "";
        if($item->type == MenuItem::TYPE['module']){
            $item = json_decode($item->data, true);
            if($item['data']['routeType'] == 'model'){
                $url = [$item['data']['route'], 'id' => $item['data']['model']];
            }elseif($item['data']['routeType'] == 'widget'){
                $url = [$item['data']['route']];
            }elseif($item['data']['routeType'] == 'action'){
                $url = [$item['data']['route']];
            }
        }else{
            $url = [$item['data']['route']];
        }
        return $url;
    }
}