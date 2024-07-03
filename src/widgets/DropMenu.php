<?php

namespace portalium\menu\widgets;

use portalium\menu\models\Menu;
use Yii;
use yii\base\Widget;
use yii\helpers\Html;
use portalium\menu\Module;
use portalium\menu\models\MenuItem;
use portalium\menu\bundles\DropMenuAsset;
use portalium\theme\widgets\Modal;
use portalium\theme\widgets\Panel;
use yii\base\DynamicModel;
use yii\helpers\ArrayHelper;
use yii\widgets\Pjax;


class DropMenu extends Widget
{
    public $items;
    public $model;
    public $id_menu;
    public $menuModel;

    public function init()
    {
        parent::init();
    }

    public function run()
    {
        echo Html::beginTag('div', ['id'=> 'spinner-div-page', 'style' => 'display: none;', 'class' => 'row']);
        echo Html::tag('div', '', ['class' => 'spinner-border text-primary col-2', 'role' => 'status']).
        Html::tag('span', 'Loading...', ['class' => 'sr-only col-2', 'style' => 'margin-left: 0px; margin-top: 5px;']);
        echo Html::endTag('div');
        echo Html::beginTag('div', ['id' => 'drop-menu-page']);
            echo Html::beginTag('div', ['class' => 'cf nestable-lists']);
                echo Html::beginTag('div', ['class' => 'dd', 'id' => 'nestable', 'style' => 'padding-right: 20px;']);
                    DropMenuAsset::register($this->getView());
                    Panel::begin([
                        'title' =>Module::t('Menu'),
                        'actions' => [
                            'header' => [
                                
                                Html::tag('button', Module::t(''), ['type' => 'button', 'data-action' => 'expand-all', 'class' => 'fa fa-expand btn btn-sm btn-primary', 'id' => 'expand-all']),
                                Html::tag('button', Module::t(''), ['type' => 'button', 'data-action' => 'collapse-all', 'class' => 'fa fa-compress btn btn-sm btn-primary', 'id' => 'collapse-all']),
                                Html::tag('button', Module::t(''), ['type' => 'button', 'class' => 'fa fa-plus btn btn-sm btn-success', 'id' => 'create-menu-item-button', 'style' => '', 'id_menu' => $this->id_menu]),
                            ],
                            'footer' => [
                                Html::tag('button', Module::t('Save'), ['type' => 'button', 'class' => 'btn btn-success', 'data-action' => 'save-sort', 'id' => 'save-sort']),
                            ]
                        ]
                    ]);
                        Pjax::begin(['id' => 'nestable-pjax']);
                            echo Html::beginTag('ol', ['class' => 'dd-list']);
                            foreach (Menu::getMenuWithChildren($this->menuModel->id_menu) as $item) {
                                echo $this->renderItem($item);
                            }
                            echo Html::endTag('ol');
                        Pjax::end();
                    Panel::end();
                echo Html::endTag('div');
                echo Html::beginTag('div', ['id'=> 'spinner-div-form', 'style' => 'display: none;']);
                    echo Html::tag('div', '', ['class' => 'spinner-border text-primary col-2', 'role' => 'status']).
                    Html::tag('span', 'Loading...', ['class' => 'sr-only col-2', 'style' => 'margin-left: 0px; margin-top: 5px;']);
                echo Html::endTag('div');
                    echo Html::beginTag('div', ['id' => 'drop-menu-form']);
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
                                echo $this->render('/web/item/_form', [
                                    'model' => $model,
                                    'id_menu' => $this->id_menu,
                                    'menuModel' => $this->menuModel,
                                ]);
                            echo Html::endTag('div');
                        Pjax::end();
                    echo Html::endTag('div');
                echo Html::endTag('div');

            echo Html::beginTag('textarea', ['id' => 'nestable-output', 'style' => 'display:none']);
            echo Html::endTag('textarea');

            Modal::begin([
                'id' => 'modal-move',
                'title' => Module::t('Move Menu Item'),
                'footer' => Html::button(Module::t('Save'), ['class' => 'btn btn-primary', 'id' => 'menu-move-item-form-button']),
            ]);
                echo $this->render('/web/item/_move', [
                    'id_menu' => $this->id_menu,
                    'menuArray' => ArrayHelper::map(Menu::find()->all(), 'id_menu', 'name'),
                    'model' => new DynamicModel(['id_item' => null, 'id_menu' => null, 'id_parent' => null]),
                ]);
            Modal::end();

            Modal::begin([
                'id' => 'modal-clone',
                'title' => Module::t('Clone Menu Item'),
                'footer' => Html::button(Module::t('Save'), ['class' => 'btn btn-primary', 'id' => 'menu-clone-item-form-button']),
            ]);
                echo $this->render('/web/item/_clone', [
                    'id_menu' => $this->id_menu,
                    'menuArray' => ArrayHelper::map(Menu::find()->all(), 'id_menu', 'name'),
                    'model' => new DynamicModel(['id_item' => null, 'id_menu' => null, 'id_parent' => null]),
                ]);
            Modal::end();

            Modal::begin([
                'id' => 'modal-delete',
                'title' => Module::t('Delete Menu Item'),
                'footer' => Html::button(Module::t('Cancel'), ['class' => 'btn btn-warning', 'data-bs-dismiss' => 'modal']).
                    Html::button(Module::t('Delete'), ['class' => 'btn btn-danger', 'id' => 'menu-delete-item-form-button']).
                    Html::button(Html::tag('span', '', ['class' => 'spinner-border spinner-border-sm', 'role' => 'status', 'aria-hidden' => 'true']).' '.Module::t('Loading...'), ['class' => 'btn btn-danger', 'id' => 'menu-delete-item-form-button-loading', 'style' => 'display: none;']),
            ]);
                echo $this->render('/web/item/_delete', [
                    'id_menu' => $this->id_menu,
                    'menuArray' => ArrayHelper::map(Menu::find()->all(), 'id_menu', 'name'),
                    'model' => new DynamicModel(['id_item' => null, 'id_menu' => null, 'id_parent' => null, 'delete_type' => null]),
                ]);
            Modal::end();
            
        echo Html::endTag('div');
    }

    protected function renderItem($item)
    {
        $html = Html::beginTag('li', ['class' => 'dd-item', 'data-id' => $item['id']]);
        $html .= Html::beginTag('div', ['class' => 'dd-handle']);
        $html .= Html::tag('button', '<i class="fa fa-arrows"></i>', ['class' => 'btn btn-sm btn-danger dd-handle-button', 'style' => 'float:left; margin-right: 5px; padding:0px 6px;']);
        $html .= Html::tag('span', $item['title']);
        $html .= Html::tag('button', '<i class="fa fa-copy"></i>', ['class' => 'btn btn-sm btn-info btn-clone clone-item', 'name' => "clone-item", 'style' => 'float:right; margin-right: 5px; padding:0px 6px;', 'data' => $item['id'], 'id_menu' => $this->id_menu]);
        $html .= Html::tag('button', '<i class="fa fa-arrow-circle-right"></i>', ['class' => 'btn btn-sm btn-warning btn-move move-item', 'name' => "move-item", 'style' => 'float:right; margin-right: 5px; padding:0px 6px;', 'data' => $item['id'], 'id_menu' => $this->id_menu]);
        $html .= Html::tag('button', '<i class="fa fa-times"></i>', ['class' => 'btn btn-sm btn-danger btn-delete delete-item', 'name' => "delete-item", 'style' => 'float:right; margin-right: 5px; padding:0px 6px;', 'data' => $item['id'], 'id_menu' => $this->id_menu]);
        $html .= Html::tag('button', '<i class="fa fa-edit"></i>', ['class' => 'btn btn-sm btn-primary btn-edit edit-item', 'name' => "edit-item", 'style' => 'float:right; margin-right: 5px; padding:0px 6px;', 'data' => $item['id'], 'id_menu' => $this->id_menu]);

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
