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

use function PHPSTORM_META\type;

class Nav extends \portalium\bootstrap5\Nav
{
    public $model;
    public $navbar;
    public $options;
    public $id;

    /**
     * @var string|null how child items are shown: 'dropdown' (Bootstrap dropdown) or 'collapse'
     * (nested list opening in place, for side menus). Null picks 'collapse' for `flex-column` menus.
     */
    public $submenuMode;

    private static $submenuCounter = 0;

    /**
     * @var int nesting level of the item being rendered in collapse mode (0 = top level)
     */
    private $depth = 0;

    public function init(): void
    {
        parent::init();

        if (!$this->model = self::findModel($this->id)) {
            throw new \yii\base\InvalidConfigException('Nav::$menu must be set.');
        }
        // $this->options['direction'] = Menu::getDirection($this->model->direction);
        $this->options['class'] .= ' ' . Menu::getDirection($this->model->direction);

        if ($this->submenuMode === null) {
            $classes = preg_split('/\s+/', (string) $this->options['class']);
            $this->submenuMode = in_array('flex-column', $classes, true) ? 'collapse' : 'dropdown';
        }
    }

    public function run(): string
    {
        $items = [];
        foreach ($this->sortItems($this->model->items) as $item) {
            if (!isset($item->parent)) {
                $url = $this->getUrl($item);
                $data = json_decode($item->data, true);
                if ($item->type == MenuItem::TYPE['module']) {
                    if ($data["data"]["routeType"] == "widget") {
                        if (property_exists($data["data"]["route"]::className(), 'icon')) {
                            $visible = (($item->name_auth != null || $item->name_auth != '') && $item->name_auth != 'guest') ? (Yii::$app->user->can($item->name_auth, ['id_module' => $item->module ?? null]) || Yii::$app->workspace->can($item->module ?? null, $item->name_auth) || Yii::$app->workspace->can($item->module ?? null, $item->name_auth . 'Own')) : ($item->name_auth == 'guest' || $item->name_auth == '' ? true : false);
                            if ($visible)
                                $items[] = $data["data"]["route"]::widget([
                                    'icon' => $this->getIcon($item),
                                    'display' => ($item->display != '') ? MenuItem::TYPE_DISPLAY['icon'] : $item->display,
                                ]);
                        } else {
                            $visible = (($item->name_auth != null || $item->name_auth != '') && $item->name_auth != 'guest') ? (Yii::$app->user->can($item->name_auth, ['id_module' => $item->module ?? null]) || Yii::$app->workspace->can($item->module ?? null, $item->name_auth) || Yii::$app->workspace->can($item->module ?? null, $item->name_auth . 'Own')) : ($item->name_auth == 'guest' || $item->name_auth == '' ? true : false);
                            if ($visible)
                            $items[] = $data["data"]["route"]::widget();
                        }
                    } else {
                        $items[] = [
                            'label' => $this->generateLabel($item, false),
                            'icon' => $this->getIcon($item),
                            'url' => $url,
                            'items' => $this->getChildItems($item->id_item),
                            'visible' => (($item->name_auth != null || $item->name_auth != '') && $item->name_auth != 'guest') ? (Yii::$app->user->can($item->name_auth, ['id_module' => $item->module ?? null]) || Yii::$app->workspace->can($item->module ?? null, $item->name_auth) || Yii::$app->workspace->can($item->module ?? null, $item->name_auth . 'Own')) : ($item->name_auth == 'guest' || $item->name_auth == '' ? true : false),
                            'sort' => $item->sort,
                            'id' => $item->id_item,
                            'group' => $item->type == MenuItem::TYPE['group'],
                            'displayType' => MenuItem::getDisplays()[($item->display != 0 && $item->display != '') ? $item->display : MenuItem::TYPE_DISPLAY['text']],
                            'placement' => MenuItem::getPlacements()[($item->placement != 0 && $item->placement != '') != 0 ? $item->placement : MenuItem::LABEL_PLACEMENT['side-by-side']],
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
                            'id' => $item->id_item,
                            'group' => $item->type == MenuItem::TYPE['group'],
                            'displayType' => MenuItem::getDisplays()[($item->display != 0 && $item->display != '') != 0 ? $item->display : MenuItem::TYPE_DISPLAY['text']],
                            'placement' => MenuItem::getPlacements()[($item->placement != 0 && $item->placement != '') != 0 ? $item->placement : MenuItem::LABEL_PLACEMENT['side-by-side']],
                        ];
                }
            }
        }

        //$items = $this->sortItems($items);
        $this->items = $items;
        //$this->encodeLabels = false;

        //BootstrapAsset::register($this->getView());

        return $this->renderItems();
    }

    public function getChildItems($id_parent)
    {
        $items = [];
        foreach ($this->sortItems($this->model->items) as $item) {
            if (isset($item->parent) && $item->parent->id_item == $id_parent) {
                $url = $this->getUrl($item);
                $visible = (($item->name_auth != null || $item->name_auth != '') && $item->name_auth != 'guest') ? (Yii::$app->user->can($item->name_auth, ['id_module' => $item->module ?? null]) || Yii::$app->workspace->can($item->module ?? null, $item->name_auth) || Yii::$app->workspace->can($item->module ?? null, $item->name_auth . 'Own')) : ($item->name_auth == 'guest' || $item->name_auth == '' ? true : false);
                
                $data = json_decode($item->data, true);
                if ($item->type == MenuItem::TYPE['module'] && $data["data"]["routeType"] == "widget" && !$visible) {
                    // a widget the user may not see is left out, like a link
                    continue;
                }
                $itemTemp = ($item->type == MenuItem::TYPE['module'] && $data["data"]["routeType"] == "widget") ?
                    $data["data"]["route"]::widget() :
                    [
                        'label' => $this->generateLabel($item, true),
                        'icon' => $this->getIcon($item),
                        'url' => $url,
                        'id' => $item->id_item,
                        'group' => $item->type == MenuItem::TYPE['group'],
                        'visible' => (($item->name_auth != null || $item->name_auth != '') && $item->name_auth != 'guest') ? (Yii::$app->user->can($item->name_auth, ['id_module' => $item->module ?? null]) || Yii::$app->workspace->can($item->module ?? null, $item->name_auth) || Yii::$app->workspace->can($item->module ?? null, $item->name_auth . 'Own')) : ($item->name_auth == 'guest' || $item->name_auth == '' ? true : false),
                        'displayType' => MenuItem::getDisplays()[($item->display != 0 && $item->display != '') != 0 ? $item->display : MenuItem::TYPE_DISPLAY['text']],
                        'placement' => MenuItem::getPlacements()[($item->placement != 0 && $item->placement != '') != 0 ? $item->placement : MenuItem::LABEL_PLACEMENT['side-by-side']],
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

    /**
     * If the item's module is registered, its translation is used; otherwise, the menu module's translation is used.
     * A single menu item linked to a non-registered module should not crash the page.
     */
    private function translateLabel($item)
    {
        $module = isset($item->module) ? Yii::$app->getModule($item->module) : null;
        return $module !== null && method_exists($module, 't') ? $module->t($item->label) : Module::t($item->label);
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
                    $label = $this->translateLabel($item);
                    break;
                case MenuItem::TYPE_DISPLAY['text']:
                    $label = $this->translateLabel($item);
                    break;
                default:
                    $label = $this->translateLabel($item);
                    break;
            }
        } else {
            $label = $this->translateLabel($item);
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
        } elseif ($item->type == MenuItem::TYPE['group']) {
            $url = '#';
        } else {
            $url = $item->url;
        }
        return $url;
    }

    public function sortItems($items)
    {
        usort($items, static function ($first_item, $second_item)
        {
            $leftHasSort = isset($first_item->sort);
            $rightHasSort = isset($second_item->sort);

            if(!($leftHasSort && $rightHasSort))
            {
                return strcasecmp((string) ($first_item->label ?? ''), (string) ($second_item->label ?? ''));
            } 

            $first_sort = (int) $first_item->sort;
            $second_sort = (int) $second_item->sort;

            if ($first_sort !== $second_sort)
            {
                return $first_sort < $second_sort ? -1 : 1;
            }

            return strcasecmp((string) ($first_item->label ?? ''), (string) ($second_item->label ?? ''));
        });

        return $items;
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
            // a widget's own HTML; decoding it would turn the text it escaped back into markup
            return $item;
        }
        if (!isset($item['label'])) {
            throw new InvalidConfigException("The 'label' option is required.");
        }
        $encodeLabel = $item['encode'] ?? $this->encodeLabels;
        $label = $encodeLabel ? Html::encode($item['label']) : $item['label'];

        $options = ArrayHelper::getValue($item, 'options', []);
        $items = ArrayHelper::getValue($item, 'items');
        $url = ArrayHelper::getValue($item, 'url', '#');

        if($item['url']==null){
            throw new InvalidConfigException("The 'url' option is required.");
        }
    
        $linkOptions = ArrayHelper::getValue($item, 'linkOptions', []);
        $disabled = ArrayHelper::getValue($item, 'disabled', false);
        $active = $this->isItemActive($item);

        if (!empty($item['group']) && !$this->hasVisibleChild($items)) {
            // a group with nothing the user can see is left out
            return '';
        }

        if (!empty($items) && is_array($items) && $this->submenuMode === 'collapse') {
            return $this->renderCollapseItem($item, $label, $items, $active, $options, $linkOptions);
        }

        if (empty($items)) {
            $items = '';
            Html::addCssClass($options, ['widget' => 'nav-item me-lg-0']);
            Html::addCssClass($linkOptions, ['widget' => 'nav-link']);
            if ($this->submenuMode === 'collapse' && !isset($linkOptions['title'])) {
                // the collapsed side menu shows only icons; the name appears on hover
                $linkOptions['title'] = Html::decode(strip_tags($label));
            }
        } else {
            $linkOptions['data']['bs-toggle'] = 'dropdown';
            $linkOptions['role'] = 'button';
            $linkOptions['aria']['expanded'] = 'false';
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

        if (isset($item['placement']))
            $options['data-bs-placement'] = $item['placement'];

        if (!isset($item['icon']))
            return Html::tag('li', Html::a('<span>' . $label . '</span>', $url, $linkOptions) . $items, $options);
        else
            return Html::tag('li', Html::a($item['icon'] . '<span>' . $label . '</span>', $url, $linkOptions) . $items, $options);
    }

    /**
     * Renders an item with children as a toggle and a nested list that opens in place.
     * The item only opens and closes its list, even when it has a URL of its own.
     * A group on the top level of the side menu is a section heading; any other item with
     * children (and a group deeper down) is a row with its icon and an arrow.
     * Every list starts open.
     */
    protected function renderCollapseItem($item, $label, $items, $active, $options, $linkOptions): string
    {
        $items = $this->isChildActive($items, $active);
        $active = $active || $this->hasActiveChild($items);
        $section = $this->depth === 0 && !empty($item['group']);
        // every list starts open; the user closes what they do not need
        $open = true;
        $submenuId = 'menu-' . $this->id . '-sub-' . (++self::$submenuCounter);
        $indicator = Html::tag('span', '', ['class' => 'submenu-indicator', 'aria-hidden' => 'true']);
        $toggle = [
            'data-bs-toggle' => 'collapse',
            'data-bs-target' => '#' . $submenuId,
            'aria-controls' => $submenuId,
            'aria-expanded' => $open ? 'true' : 'false',
        ];

        if ($section) {
            Html::addCssClass($options, ['widget' => 'nav-item nav-section']);
            $options['data-section'] = $item['id'] ?? $label;
            if ($active) {
                Html::addCssClass($options, ['activate' => 'has-active']);
            }
            $head = Html::a(
                Html::tag('span', $label, ['class' => 'nav-section-label']) . $indicator,
                '#' . $submenuId,
                array_merge(['class' => 'nav-section-toggle'], $toggle, ['role' => 'button'])
            );
        } else {
            Html::addCssClass($options, ['widget' => 'nav-item has-submenu']);
            Html::addCssClass($linkOptions, ['widget' => 'nav-link submenu-toggle']);
            $head = Html::a(
                ($item['icon'] ?? '') . '<span>' . $label . '</span>' . $indicator,
                '#' . $submenuId,
                array_merge($linkOptions, $toggle, ['role' => 'button'])
            );
        }

        $children = [];
        $this->depth++;
        foreach ($items as $child) {
            if (isset($child['visible']) && !$child['visible']) {
                continue;
            }
            $children[] = $this->renderItem($child);
        }
        $this->depth--;

        if (isset($item['displayType'])) {
            $options['data-bs-type'] = $item['displayType'];
        }
        if (isset($item['placement'])) {
            $options['data-bs-placement'] = $item['placement'];
        }

        return Html::tag(
            'li',
            $head . Html::tag('ul', implode("\n", $children), [
                'id' => $submenuId,
                'class' => 'nav flex-column submenu collapse' . ($open ? ' show' : ''),
            ]),
            $options
        );
    }

    /**
     * Whether at least one child can be seen by the current user: a widget with some output, a
     * visible link, or a visible group that itself holds something visible.
     */
    protected function hasVisibleChild($items): bool
    {
        if (!is_array($items)) {
            return false;
        }
        foreach ($items as $child) {
            if (is_string($child)) {
                if (trim($child) !== '') {
                    return true;
                }
                continue;
            }
            if (!is_array($child) || !ArrayHelper::getValue($child, 'visible', true)) {
                continue;
            }
            if (empty($child['group']) || $this->hasVisibleChild($child['items'] ?? [])) {
                return true;
            }
        }

        return false;
    }

    /**
     * Whether any visible child, at any depth, is the current page.
     */
    protected function hasActiveChild(array $items): bool
    {
        foreach ($items as $child) {
            if (!is_array($child) || !ArrayHelper::getValue($child, 'visible', true)) {
                continue;
            }
            if (!empty($child['active']) || $this->isItemActive($child)) {
                return true;
            }
            if (is_array($child['items'] ?? null) && $this->hasActiveChild($child['items'])) {
                return true;
            }
        }

        return false;
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
