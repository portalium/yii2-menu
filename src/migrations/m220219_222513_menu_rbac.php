<?php

use yii\db\Migration;
use portalium\menu\Module;


class m220219_222513_menu_rbac extends Migration
{
    public function up()
    {
        $auth = Yii::$app->authManager;
        $settings = yii\helpers\ArrayHelper::map(portalium\site\models\Setting::find()->asArray()->all(),'name','value');
        $role = $settings['default::role'];
        $admin = (isset($role) && $role != '') ? $auth->getRole($role) : $auth->getRole('admin');

        $menuApiDefaultView = $auth->createPermission('menuApiDefaultView');
        $menuApiDefaultView->description = 'View menu';
        $auth->add($menuApiDefaultView);
        $auth->addChild($admin, $menuApiDefaultView);

        $menuApiDefaultCreate = $auth->createPermission('menuApiDefaultCreate');
        $menuApiDefaultCreate->description = 'Create menu';
        $auth->add($menuApiDefaultCreate);
        $auth->addChild($admin, $menuApiDefaultCreate);

        $menuApiDefaultUpdate = $auth->createPermission('menuApiDefaultUpdate');
        $menuApiDefaultUpdate->description = 'Update menu';
        $auth->add($menuApiDefaultUpdate);
        $auth->addChild($admin, $menuApiDefaultUpdate);

        $menuApiDefaultDelete = $auth->createPermission('menuApiDefaultDelete');
        $menuApiDefaultDelete->description = 'Delete menu';
        $auth->add($menuApiDefaultDelete);
        $auth->addChild($admin, $menuApiDefaultDelete);

        $menuApiDefaultIndex = $auth->createPermission('menuApiDefaultIndex');
        $menuApiDefaultIndex->description = 'View menu';
        $auth->add($menuApiDefaultIndex);
        $auth->addChild($admin, $menuApiDefaultIndex);

        $menuWebDefaultIndex = $auth->createPermission('menuWebDefaultIndex');
        $menuWebDefaultIndex->description = 'View menu';
        $auth->add($menuWebDefaultIndex);
        $auth->addChild($admin, $menuWebDefaultIndex);

        $menuWebDefaultView = $auth->createPermission('menuWebDefaultView');
        $menuWebDefaultView->description = 'View menu';
        $auth->add($menuWebDefaultView);
        $auth->addChild($admin, $menuWebDefaultView);

        $menuWebDefaultCreate = $auth->createPermission('menuWebDefaultCreate');
        $menuWebDefaultCreate->description = 'Create menu';
        $auth->add($menuWebDefaultCreate);
        $auth->addChild($admin, $menuWebDefaultCreate);

        $menuWebDefaultUpdate = $auth->createPermission('menuWebDefaultUpdate');
        $menuWebDefaultUpdate->description = 'Update menu';
        $auth->add($menuWebDefaultUpdate);
        $auth->addChild($admin, $menuWebDefaultUpdate);

        $menuWebDefaultDelete = $auth->createPermission('menuWebDefaultDelete');
        $menuWebDefaultDelete->description = 'Delete menu';
        $auth->add($menuWebDefaultDelete);
        $auth->addChild($admin, $menuWebDefaultDelete);
        
        $menuWebItemIndex = $auth->createPermission('menuWebItemIndex');
        $menuWebItemIndex->description = 'View menu item';
        $auth->add($menuWebItemIndex);
        $auth->addChild($admin, $menuWebItemIndex);

        $menuWebItemView = $auth->createPermission('menuWebItemView');
        $menuWebItemView->description = 'View menu item';
        $auth->add($menuWebItemView);
        $auth->addChild($admin, $menuWebItemView);

        $menuWebItemCreate = $auth->createPermission('menuWebItemCreate');
        $menuWebItemCreate->description = 'Create menu item';
        $auth->add($menuWebItemCreate);
        $auth->addChild($admin, $menuWebItemCreate);

        $menuWebItemUpdate = $auth->createPermission('menuWebItemUpdate');
        $menuWebItemUpdate->description = 'Update menu item';
        $auth->add($menuWebItemUpdate);
        $auth->addChild($admin, $menuWebItemUpdate);

        $menuWebItemDelete = $auth->createPermission('menuWebItemDelete');
        $menuWebItemDelete->description = 'Delete menu item';
        $auth->add($menuWebItemDelete);
        $auth->addChild($admin, $menuWebItemDelete);

        $menuWebItemRouteType = $auth->createPermission('menuWebItemRouteType');
        $menuWebItemRouteType->description = 'View menu item';
        $auth->add($menuWebItemRouteType);
        $auth->addChild($admin, $menuWebItemRouteType);

        $menuWebItemRoute = $auth->createPermission('menuWebItemRoute');
        $menuWebItemRoute->description = 'View menu item';
        $auth->add($menuWebItemRoute);
        $auth->addChild($admin, $menuWebItemRoute);

        $menuWebItemModel = $auth->createPermission('menuWebItemModel');
        $menuWebItemModel->description = 'View menu item';
        $auth->add($menuWebItemModel);
        $auth->addChild($admin, $menuWebItemModel);

    }

    public function down()
    {
        $auth = Yii::$app->authManager;

        $auth->remove($auth->getPermission('menuApiDefaultView'));
        $auth->remove($auth->getPermission('menuApiDefaultCreate'));
        $auth->remove($auth->getPermission('menuApiDefaultUpdate'));
        $auth->remove($auth->getPermission('menuApiDefaultDelete'));
        $auth->remove($auth->getPermission('menuApiDefaultIndex'));
        $auth->remove($auth->getPermission('menuWebDefaultIndex'));
        $auth->remove($auth->getPermission('menuWebDefaultView'));
        $auth->remove($auth->getPermission('menuWebDefaultCreate'));
        $auth->remove($auth->getPermission('menuWebDefaultUpdate'));
        $auth->remove($auth->getPermission('menuWebDefaultDelete'));
        $auth->remove($auth->getPermission('menuWebItemIndex'));
        $auth->remove($auth->getPermission('menuWebItemView'));
        $auth->remove($auth->getPermission('menuWebItemCreate'));
        $auth->remove($auth->getPermission('menuWebItemUpdate'));
        $auth->remove($auth->getPermission('menuWebItemDelete'));
        $auth->remove($auth->getPermission('menuWebItemRouteType'));
        $auth->remove($auth->getPermission('menuWebItemRoute'));
        $auth->remove($auth->getPermission('menuWebItemModel'));

    }
}