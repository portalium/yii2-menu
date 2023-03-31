<?php

namespace portalium\menu\widgets;

use Yii;
use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

use portalium\menu\Module;
use portalium\menu\models\Menu;
use portalium\menu\models\MenuItem;
use portalium\theme\widgets\NavBar;
use portalium\theme\widgets\Nav as BaseNav;

class Nav extends Widget
{
    public $model;
    public $navbar;
    public $options;
    public $id;

    public function init()
    {
        parent::init();
        
        if (!$this->model = self::findModel($this->id)) {
            throw new \yii\base\InvalidConfigException('Nav::$menu must be set.');
        }
    }

    public function run()
    {
        $items = [];
        foreach ($this->model->items as $item) {
            if (!isset($item->parent)) {
                $url = $this->getUrl($item);
                $data = json_decode($item->data, true);
                if ($item->type == MenuItem::TYPE['module']) {
                    $items[] =
                        ($data["data"]["routeType"] == "widget") ?
                            $data["data"]["route"]::widget() :
                            [
                                'label' => isset($item->module) ? $this->getIcon($item) . Yii::$app->getModule($item->module)->t($item->label) : $this->getIcon($item) . Module::t($item->label),
                                'url' => $url,
                                'items' => $this->getChildItems($item->id_item),
                                'visible' => (($item->name_auth != null || $item->name_auth != '') && $item->name_auth != 'guest') ? Yii::$app->user->can($item->name_auth) : ($item->name_auth == 'guest' ? true : false),
                                'sort' => $item->sort
                            ];
                } else {
                    $items[] =
                        [
                            'label' => isset($item->module) ? $this->getIcon($item) . Yii::$app->getModule($item->module)->t($item->label) : $this->getIcon($item) . Module::t($item->label),
                            'url' => $url,
                            'items' => $this->getChildItems($item->id_item),
                            'visible' => (($item->name_auth != null || $item->name_auth != '') && $item->name_auth != 'guest') ? Yii::$app->user->can($item->name_auth) : ($item->name_auth == 'guest' ? true : false),
                            'sort' => $item->sort
                        ];
                }
            }
        }

        $items = $this->sortItems($items);
        return BaseNav::widget([
            'items' => $items,
            'options' => $this->options
        ]);
    }

    public function getChildItems($id_parent)
    {
        $items = [];
        foreach ($this->model->items as $item) {
            if (isset($item->parent) && $item->parent->id_item == $id_parent) {

                $url = $this->getUrl($item);
                $data = json_decode($item->data, true);
                $itemTemp = ($item->type == MenuItem::TYPE['module'] && $data["data"]["routeType"] == "widget") ?
                    $data["data"]["route"]::widget() :
                    [
                        'label' =>isset($item->module) ? $this->getIcon($item) . Yii::$app->getModule($item->module)->t($item->label) : $this->getIcon($item) . Module::t($item->label),
                        'url' => $url,
                        'visible' => (($item->name_auth != null || $item->name_auth != '') && $item->name_auth != 'guest') ? Yii::$app->user->can($item->name_auth) : ($item->name_auth == 'guest' ? true : false),
                    ];
                $list = $this->getChildItems($item->id_item);
                if (!empty($list)) {
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
        if ($item->type == MenuItem::TYPE['module']) {
            $item = json_decode($item->data, true);
            if ($item['data']['routeType'] == 'model') {
                $url = [$item['data']['route'], 'id' => $item['data']['model']];
            } elseif ($item['data']['routeType'] == 'widget') {
                $url = [$item['data']['route']];
            } elseif ($item['data']['routeType'] == 'action') {
                $url = [$item['data']['route']];
            }
        } else if($item->type == MenuItem::TYPE['route']) {
            $item = json_decode($item->data, true);
            $url = [$item['data']['route']];
        } else if($item->type == MenuItem::TYPE['url']) {
            $item = json_decode($item->data, true);
            $url = $item['data']['url'];
        }else{
            $url = $item->url;
        }
        return $url;
    }

    public function sortItems($items)
    {
        $sort = [];
        foreach ($items as $item) {
            if (isset($item['sort']))
                $sort[$item['sort']] = $item;
            else
                $sort[] = $item;
        }
        ksort($sort);
        return $sort;
    }

    public function getIcon($item)
    {
        $style = json_decode($item->style, true);
        $icon = isset($style['icon']) ? $style['icon'] : '';
        $color = isset($style['color']) ? $style['color'] : '';
        $size = isset($style['iconSize']) ? $style['iconSize'] : '';
        Yii::warning($icon . ' ' . $color . ' ' . $size);
        return Html::tag('i', '', ['class' => $icon, 'style' => 'color:' . $color . '; margin-right: 5px; font-size:' . $size . 'px;']);
    }

    private function findModel($id)
    {
        if (($model = Menu::findOne(['id_menu' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Module::t('The requested menu does not exist.'));
    }
}
