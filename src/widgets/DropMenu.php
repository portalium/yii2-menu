<?php

namespace portalium\menu\widgets;

use portalium\menu\models\Menu;
use portalium\theme\widgets\Panel;
use Yii;
use yii\base\Widget;
use yii\helpers\Html;
use portalium\menu\Module;
use portalium\menu\models\MenuItem;
use portalium\menu\bundles\DropMenuAsset;
use yii\widgets\Pjax;


class DropMenu extends Widget
{
    public $items;
    public $model;
    public $id_menu;
    public $menu;

    public function init()
    {
        $this->menu = Menu::findOne($this->id_menu);
        parent::init();
    }

    public function run()
    {
        echo Html::beginTag('div', ['class' => 'cf nestable-lists']);
        Pjax::begin(['id' => 'nestable-pjax']);
        echo Html::beginTag('div', ['class' => 'dd', 'id' => 'nestable']);
        Panel::begin([
            'title' => $this->menu->name,
            'actions' => [
                'header' => [
                    Html::beginTag('menu', ['id' => 'nestable-menu', 'class' => 'nestable-menu']),
                    Html::tag('button', Module::t(''), ['type' => 'button', 'data-action' => 'expand-all', 'class' => 'btn btn-sm btn-primary fa fa-expand', 'id' => 'expand-all']),
                    Html::tag('button', Module::t(''), ['type' => 'button', 'data-action' => 'collapse-all', 'class' => 'btn btn-sm btn-primary fa fa-compress', 'id' => 'collapse-all']),
                    Html::endTag('menu')
                ],
                'footer' => [
                    Html::beginTag('menu', ['class' => 'nestable-menu']),
                    Html::tag('button', Module::t('Save Menu'), ['type' => 'button', 'class' => 'btn btn-sm btn-success', 'data-action' => 'save-sort', 'id' => 'save-sort']),
                    Html::endTag('menu')
                    ]
            ]
        ]);
        echo Html::beginTag('ol', ['class' => 'dd-list']);

        foreach (Menu::getMenuWithChildren('web-menu') as $item) {
            echo $this->renderItem($item);
        }

        echo Html::endTag('ol');
        Panel::end();
        echo Html::endTag('div');
        Pjax::end();
        
        Pjax::begin(['id' => 'nestable2-pjax']);
        echo Html::beginTag('div', ['class' => 'dd', 'id' => 'nestable2']);
        
        $model = new MenuItem();
        if (Yii::$app->request->isGet) {
            $id_item = Yii::$app->request->get('id_item');
            if ($id_item) {
                $model = MenuItem::findOne($id_item);
                if (!$model) {
                    $model = new MenuItem();
                }
            }
        }
        echo $this->render('/backend/item/_form', [
            'model' => $model,
            'id_menu' => $this->id_menu,
        ]);
        echo Html::endTag('div');
        Pjax::end();
        echo Html::endTag('div');
        echo Html::endTag('div');
        echo Html::beginTag('textarea', ['id' => 'nestable-output', 'style' => 'display:none']);
        echo Html::endTag('textarea');
        
    }

    protected function renderItem($item)
    {
        $html = Html::beginTag('li', ['class' => 'dd-item', 'data-id' => $item['id']]);
        $html .= Html::beginTag('div', ['class' => 'dd-handle']);
        $html .= Html::tag('button', '<i class="fa fa-arrows"></i>', ['class' => 'btn btn-sm btn-danger dd-handle-button', 'style' => 'float:left; margin-right:5px; padding:0px 6px;']);
        $html .= Html::tag('span', $item['title']);
        $html .= Html::tag('button', '<i class="fa fa-times"></i>', ['class' => 'btn btn-sm btn-danger btn-delete delete-item', 'name' => "delete-item", 'style' => 'float:right; margin-right:5px; padding:0px 6px;', 'data' => $item['id']]);
        $html .= Html::tag('button', '<i class="fa fa-edit"></i>', ['class' => 'btn btn-sm btn-primary btn-edit edit-item', 'name' => "edit-item", 'style' => 'float:right; margin-right:5px; padding:0px 6px;', 'data' => $item['id']]);
        $html .= Html::endTag('div');
        if (isset($item['hasChildren']) && $item['hasChildren']) {
            $html .= Html::beginTag('ol', ['class' => 'dd-list']);
            foreach ($item['children'] as $child) {
                $html .= $this->renderItem($child);
            }
            $html .= Html::endTag('ol');
        }
        //delete button
        $html .= Html::endTag('li');
        return $html;
    }

}
    