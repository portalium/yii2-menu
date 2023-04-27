<?php

namespace portalium\menu\widgets;

use Yii;
use yii\base\Widget;
use yii\helpers\Html;
use yii\web\NotFoundHttpException;
use portalium\menu\Module;
use portalium\menu\models\Menu;
use portalium\menu\models\MenuItem;
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
        Yii::$app->view->registerCss(
            '
            
            '
        );
    }

    public function run()
    {
        $items = [];
        foreach ($this->model->items as $item) {
            if (!isset($item->parent)) {
                $url = $this->getUrl($item);
                $data = json_decode($item->data, true);
                if ($item->type == MenuItem::TYPE['module']) {
                    if ($data["data"]["routeType"] == "widget"){
                        if (property_exists($data["data"]["route"]::className(), 'icon')) {
                            $items[] = $data["data"]["route"]::widget([
                                'icon' => $this->getIcon($item),
                                'display' => ($item->display != '') ? MenuItem::TYPE_DISPLAY['icon'] : $item->display,
                            ]);
                        } else {
                            $items[] = $data["data"]["route"]::widget();
                        }
                    }else{
                    $items[] = [
                                'label' => $this->generateLabel($item, false),
                                'icon' => $this->getIcon($item),
                                'url' => $url,
                                'items' => $this->getChildItems($item->id_item),
                                'visible' => (($item->name_auth != null || $item->name_auth != '') && $item->name_auth != 'guest') ? Yii::$app->user->can($item->name_auth) : ($item->name_auth == 'guest' ? true : false),
                                'sort' => $item->sort,
                                'displayType' => MenuItem::getDisplays()[($item->display != 0 && $item->display != '')? $item->display : MenuItem::TYPE_DISPLAY['text']],
                            ];
                        }
                } else {
                    $items[] =
                        [
                            'label' => $this->generateLabel($item, false),
                            'icon' => $this->getIcon($item),
                            'url' => $url,
                            'items' => $this->getChildItems($item->id_item),
                            'visible' => (($item->name_auth != null || $item->name_auth != '') && $item->name_auth != 'guest') ? Yii::$app->user->can($item->name_auth) : ($item->name_auth == 'guest' ? true : false),
                            'sort' => $item->sort,
                            'displayType' => MenuItem::getDisplays()[($item->display != 0 && $item->display != '') != 0 ? $item->display : MenuItem::TYPE_DISPLAY['text']],
                        ];
                }
            }
        }

        $items = $this->sortItems($items);
        return BaseNav::widget([
            'items' => $items,
            'options' => $this->options,
            'encodeLabels' => false,
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
                        'label' => $this->generateLabel($item, true),
                        'icon' => $this->getIcon($item),
                        'url' => $url,
                        'visible' => (($item->name_auth != null || $item->name_auth != '') && $item->name_auth != 'guest') ? Yii::$app->user->can($item->name_auth) : ($item->name_auth == 'guest' ? true : false),
                        'displayType' => MenuItem::getDisplays()[($item->display != 0 && $item->display != '') != 0 ? $item->display : MenuItem::TYPE_DISPLAY['text']],
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

    private function generateLabel($item, $isChild = false)
    {
        $label = "";
            if(isset($item->display)){
                switch ($item->display) {
                    case MenuItem::TYPE_DISPLAY['icon']:
                        $label = '';
                        break;
                    case MenuItem::TYPE_DISPLAY['icon-text']:
                        $label = isset($item->module) ? Yii::$app->getModule($item->module)->t($item->label) : Module::t($item->label);
                        break;
                    case MenuItem::TYPE_DISPLAY['text']:
                        $label = isset($item->module) ? Yii::$app->getModule($item->module)->t($item->label) : Module::t($item->label);
                        break;
                    default:
                        $label = isset($item->module) ? Yii::$app->getModule($item->module)->t($item->label) : Module::t($item->label);
                        break;
                }
            }else{
                $label = isset($item->module) ? Yii::$app->getModule($item->module)->t($item->label) : Module::t($item->label);
            }

        return $label;
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
        if (isset($item->display) && $item->display == MenuItem::TYPE_DISPLAY['text']) {
            $icon = '';
            $color = '';
            $size = '';
        }

        return Html::tag('i', '', ['class' => 'fa '. $icon, 'style' => 'min-width:25px; color:' . $color . '; font-size:' . $size . 'px; ']);
    }

    private function findModel($id)
    {
        if (($model = Menu::findOne(['id_menu' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Module::t('The requested menu does not exist.'));
    }
}
