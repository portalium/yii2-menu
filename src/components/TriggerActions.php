<?php

namespace portalium\menu\components;

use portalium\menu\models\Menu;
use portalium\menu\models\MenuItem;
use portalium\menu\Module;
use portalium\user\components\UserDataHandler;
use portalium\user\components\UserDeleteException;
use yii\base\BaseObject;

class TriggerActions extends BaseObject
{
    public function init(): void
    {
        parent::init();
    }

    public function onUserDeleteBefore($event)
    {
        [$idUser, $action, $defaultUser] = UserDataHandler::payload($event);

        if ($action === UserDataHandler::ACTION_DELETE) {
            $menuIds = Menu::find()->select('id_menu')->where(['id_user' => $idUser])->column();
            foreach (MenuItem::find()->where(['id_menu' => $menuIds])->all() as $item) {
                if ($item->delete() === false) {
                    throw new UserDeleteException(Module::t('Could not delete menu item {label}.', ['label' => $item->label]));
                }
            }
        }

        UserDataHandler::apply($action, MenuItem::class, $idUser, $defaultUser);
        UserDataHandler::apply($action, Menu::class, $idUser, $defaultUser);
    }
}
