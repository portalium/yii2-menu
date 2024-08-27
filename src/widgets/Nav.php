<?php

namespace portalium\menu\widgets;

use portalium\bootstrap5\BootstrapAsset;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\NotFoundHttpException;
use portalium\menu\Module;
use portalium\menu\models\Menu;
use portalium\menu\models\MenuItem;
use yii\base\Model;

use function PHPSTORM_META\type;

class Nav extends \portalium\bootstrap5\Nav
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
        // $this->options['direction'] = Menu::getDirection($this->model->direction);
        $this->options['class'] .= ' ' . Menu::getDirection($this->model->direction);
    }

    public function run(): string
    {
        $items = [];
        foreach ($this->model->items as $item) {
            if (!isset($item->parent)) {
                $url = $this->getUrl($item);
                $data = json_decode($item->data, true);
                if ($item->type == MenuItem::TYPE['module']) {
                    if ($data["data"]["routeType"] == "widget") {

                        if (property_exists($data["data"]["route"]::className(), 'icon')) {
                            $visible = (($item->name_auth != null || $item->name_auth != '') && $item->name_auth != 'guest') ? (Yii::$app->user->can($item->name_auth, ['id_module' => $item->module ?? null]) || Yii::$app->workspace->can($item->module ?? null, $item->name_auth) || Yii::$app->workspace->can($item->module ?? null, $item->name_auth . 'Own')) : ($item->name_auth == 'guest' || $item->name_auth == '' ? true : false);
                            if ($visible) {
                                if (!($item->placement != 0 && $item->placement != '') || $item['placement'] == MenuItem::LABEL_PLACEMENT["default"]) {
                                    $item['placement'] = $this->model['placement'];

                                }
                                $items[] = $data["data"]["route"]::widget([
                                    'icon' => $this->getIcon($item),
                                    'display' => ($item->display != '') ? MenuItem::TYPE_DISPLAY['icon-text'] : $item->display,
                                    'placement' => MenuItem::getPlacements()[ $item->placement],

                                ]);
                            }
                        } else {
                            $visible = (($item->name_auth != null || $item->name_auth != '') && $item->name_auth != 'guest') ? (Yii::$app->user->can($item->name_auth, ['id_module' => $item->module ?? null]) || Yii::$app->workspace->can($item->module ?? null, $item->name_auth) || Yii::$app->workspace->can($item->module ?? null, $item->name_auth . 'Own')) : ($item->name_auth == 'guest' || $item->name_auth == '' ? true : false);
                            if ($visible) {
                                if (!($item->placement != 0 && $item->placement != '') || $item['placement'] == MenuItem::LABEL_PLACEMENT["default"]) {
                                    $item['placement'] = $this->model['placement'];
                                }
                                $items[] = $data["data"]["route"]::widget([
                                    'placement' => MenuItem::getPlacements()[ $item->placement],
                                    'display' => MenuItem::getDisplays()[($item->display != 0 && $item->display != '') ? $item->display : MenuItem::TYPE_DISPLAY['icon-text']],
                                ]);
                            }
                        }
                    } else {
                        $items[] = [
                            'label' => $this->generateLabel($item, false),
                            'icon' => $this->getIcon($item),
                            'url' => $url,
                            'items' => $this->getChildItems($item->id_item),
                            'visible' => (($item->name_auth != null || $item->name_auth != '') && $item->name_auth != 'guest') ? (Yii::$app->user->can($item->name_auth, ['id_module' => $item->module ?? null]) || Yii::$app->workspace->can($item->module ?? null, $item->name_auth) || Yii::$app->workspace->can($item->module ?? null, $item->name_auth . 'Own')) : ($item->name_auth == 'guest' || $item->name_auth == '' ? true : false),
                            'sort' => $item->sort,
                            'displayType' => MenuItem::getDisplays()[($item->display != 0 && $item->display != '') ? $item->display : MenuItem::TYPE_DISPLAY['icon-text']],
                            'placement' => ($item->placement != 0 && $item->placement != '') != 0 ? $item->placement : MenuItem::LABEL_PLACEMENT['default'],
                        ];
                    }
                } else {
                    $items[] =
                        [
                            'label' => $this->generateLabel($item, false),
                            'icon' => $this->getIcon($item),
                            'url' => $url,
                            'items' => $this->getChildItems($item->id_item),
                            'visible' => (($item->name_auth != null || $item->name_auth != '') && $item->name_auth != 'guest') ? (Yii::$app->user->can($item->name_auth, ['id_module' => $item->module ?? null]) || Yii::$app->workspace->can($item->module ?? null, $item->name_auth) || Yii::$app->workspace->can($item->module ?? null, $item->name_auth . 'Own')) : ($item->name_auth == 'guest' || $item->name_auth == '' ? true : false),
                            'sort' => $item->sort,
                            'displayType' => MenuItem::getDisplays()[($item->display != 0 && $item->display != '') != 0 ? $item->display : MenuItem::TYPE_DISPLAY['icon-text']],
                            'placement' => ($item->placement != 0 && $item->placement != '') != 0 ? $item->placement : MenuItem::LABEL_PLACEMENT['default'],
                        ];
                }
            }
        }

        $items = $this->sortItems($items);
        $this->items = $items;
        //$this->encodeLabels = false;

        //BootstrapAsset::register($this->getView());

        return $this->renderItems();
    }

    public function getChildItems($id_parent)
    {
        $items = [];
        foreach ($this->model->items as $item) {
            if (isset($item->parent) && $item->parent->id_item == $id_parent) {
                $url = $this->getUrl($item);
                $visible = (($item->name_auth != null || $item->name_auth != '') && $item->name_auth != 'guest') ? (Yii::$app->user->can($item->name_auth, ['id_module' => $item->module ?? null]) || Yii::$app->workspace->can($item->module ?? null, $item->name_auth) || Yii::$app->workspace->can($item->module ?? null, $item->name_auth . 'Own')) : ($item->name_auth == 'guest' || $item->name_auth == '' ? true : false);

                $data = json_decode($item->data, true);
                $itemTemp = ($item->type == MenuItem::TYPE['module'] && $data["data"]["routeType"] == "widget") ?
                    $data["data"]["route"]::widget() :
                    [
                        'label' => $this->generateLabel($item, true),
                        'icon' => $this->getIcon($item),
                        'url' => $url,
                        'visible' => (($item->name_auth != null || $item->name_auth != '') && $item->name_auth != 'guest') ? (Yii::$app->user->can($item->name_auth, ['id_module' => $item->module ?? null]) || Yii::$app->workspace->can($item->module ?? null, $item->name_auth) || Yii::$app->workspace->can($item->module ?? null, $item->name_auth . 'Own')) : ($item->name_auth == 'guest' || $item->name_auth == '' ? true : false),
                        'displayType' => MenuItem::getDisplays()[($item->display != 0 && $item->display != '') != 0 ? $item->display : MenuItem::TYPE_DISPLAY['icon-text']],
                        'placement' => ($item->placement != 0 && $item->placement != '') != 0 ? $item->placement : MenuItem::LABEL_PLACEMENT['default'],
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
        if (isset($item->display)) {
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
        } else {
            $label = isset($item->module) ? Yii::$app->getModule($item->module)->t($item->label) : Module::t($item->label);
        }

        return $label;
    }

    public function getUrl($item)
    {
        $url = "#";
        if ($item->type == MenuItem::TYPE['module']) {
            $item = json_decode($item->data, true);
            if ($item['data']['routeType'] == 'model') {
                $url = [$item['data']['route'], 'id' => $item['data']['model']];
            } elseif ($item['data']['routeType'] == 'widget') {
                $url = [$item['data']['route']];
            } elseif ($item['data']['routeType'] == 'action') {
                $url = [$item['data']['route']];
            }
        } else if ($item->type == MenuItem::TYPE['route']) {
            $item = json_decode($item->data, true);
            $url = [$item['data']['route']];
        } else if ($item->type == MenuItem::TYPE['url']) {
            $item = json_decode($item->data, true);
            $url = $item['data']['url'];
        } else {
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
            return '';
        }
        return Html::tag('i', '', ['class' => 'fa ' . $icon, 'style' => 'min-width:25px; color:' . $color . '; font-size:' . $size . 'px; ']);
    }

    private function findModel($id)
    {
        if (($model = Menu::findOne(['id_menu' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Module::t('The requested menu does not exist.'));
    }

    public function renderItem($item): string
    {

        if (is_string($item)) {
            return Html::decode($item);
        }
        if (!isset($item['label'])) {
            throw new InvalidConfigException("The 'label' option is required.");
        }
        $encodeLabel = $item['encode'] ?? $this->encodeLabels;
        $label = $encodeLabel ? Html::encode($item['label']) : $item['label'];

        $options = ArrayHelper::getValue($item, 'options', []);
        $items = ArrayHelper::getValue($item, 'items');
        $url = ArrayHelper::getValue($item, 'url', '#');

        if ($item['url'] == null) {
            throw new InvalidConfigException("The 'url' option is required.");
        }

        $linkOptions = ArrayHelper::getValue($item, 'linkOptions', []);
        $disabled = ArrayHelper::getValue($item, 'disabled', false);
        $active = $this->isItemActive($item);

        if (empty($items)) {
            $items = '';
            Html::addCssClass($options, ['widget' => 'nav-item me-lg-0']);
            Html::addCssClass($linkOptions, ['widget' => 'nav-link']);
        } else {
            $linkOptions['data']['bs-toggle'] = 'dropdown';
            $linkOptions['role'] = 'button';
            $linkOptions['aria']['expanded'] = 'false';
            //Html::addCssClass($linkOptions, ['widget' => 'nav-link']);
            Html::addCssClass($options, ['widget' => 'dropdown nav-item']);
            Html::addCssClass($linkOptions, ['widget' => 'dropdown-toggle nav-link']);
            if (is_array($items)) {
                $items = $this->isChildActive($items, $active);
                $items = $this->renderDropdown($items, $item);
            }
        }

        if ($disabled) {
            ArrayHelper::setValue($linkOptions, 'tabindex', '-1');
            ArrayHelper::setValue($linkOptions, 'aria.disabled', 'true');
            Html::addCssClass($linkOptions, ['disable' => 'disabled']);
        } elseif ($this->activateItems && $active) {
            Html::addCssClass($linkOptions, ['activate' => 'active']);
        }
        if (isset($item['displayType']))
            $options['data-bs-type'] = $item['displayType'];

        if (isset($item['placement'])) {
            if ($item['placement'] == '3' || $item['placement'] == "") {
                if ($this->model['placement'] == '1') {
                    $plc = Menu::getPlacements()[Menu::LABEL_PLACEMENT['side-by-side']];
                    $options['data-bs-placement'] = $plc;
                    Html::addCssClass($options, 'placement');
                } else {
                    $plc = Menu::getPlacements()[Menu::LABEL_PLACEMENT['top-to-bottom']];
                    $options['data-bs-placement'] = $plc;
                    Html::addCssClass($options, 'placement');
                }
            } else {
                $options['data-bs-placement'] = $item['placement'];
                Html::addCssClass($options, 'placement');
            }
        }


        if (!isset($item['icon']))
            return Html::tag('li', Html::a('<span>' . $label . '</span>', $url, $linkOptions) . $items, $options);
        else
            return Html::tag('li', Html::a($item['icon'] . '<span>' . $label . '</span>', $url, $linkOptions) . $items, $options);
    }

    public function renderItems(): string
    {
        $items = [];
        foreach ($this->items as $item) {
            if (isset($item['visible']) && !$item['visible']) {
                continue;
            }
            $items[] = $this->renderItem($item);
        }

        return Html::tag('ul', implode("\n", $items), $this->options);
    }
}
