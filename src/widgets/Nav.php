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
                if($item->type == MenuItem::TYPE['module']){
                    $items[] = 
                        ($data["data"]["routeType"] == "widget") ? 
                        $data["data"]["route"]::widget() :
                            [
                                'label' => $item->label,
                                'url' => $url,
                                'items' => $this->getChildItems($item->id_item),
                                'visible' => ($item->name_auth != null || $item->name_auth != '') ? Yii::$app->user->can($item->name_auth) : 1,
                                'sort' => $item->sort
                            ];
                }else{
                    $items[] = 
                            [
                                'label' => $item->label,
                                'url' => $url,
                                'items' => $this->getChildItems($item->id_item),
                                'visible' => ($item->name_auth != null || $item->name_auth != '') ? Yii::$app->user->can($item->name_auth) : true,
                                'sort' => $item->sort
                            ];
                }

            }
        }
       
        
        $items = $this->sortItems($items);
        
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
                    'visible' => ($item->name_auth != null || $item->name_auth != '') ? Yii::$app->user->can($item->name_auth) : 1,
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
    
    public function getUrl($item)
    {
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
            $item = json_decode($item->data, true);
            $url = [$item['data']['route']];
        }
        return $url;
    }

    public function sortItems($items)
    {
        $sort = [];
        foreach ($items as $item) {
            if(isset($item['sort']))
                $sort[$item['sort']] = $item;
            else
                $sort[] = $item;
        }
        ksort($sort);
        return $sort;
    }
    
}