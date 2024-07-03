<?php

use portalium\db\Migration;


class m220219_222513_menu_rbac extends Migration
{
    public function up()
    {
        $auth = Yii::$app->authManager;
    
        $role = Yii::$app->setting->getValue('site::admin_role');
        $admin = (isset($role) && $role != '') ? $auth->getRole($role) : $auth->getRole('admin');

        $menuApiDefaultView = $auth->createPermission('menuApiDefaultView');
        $menuApiDefaultView->description = 'Menu Api Default View';
        $auth->add($menuApiDefaultView);
        $auth->addChild($admin, $menuApiDefaultView);

        $menuApiDefaultCreate = $auth->createPermission('menuApiDefaultCreate');
        $menuApiDefaultCreate->description = 'Menu Api Default Create';
        $auth->add($menuApiDefaultCreate);
        $auth->addChild($admin, $menuApiDefaultCreate);

        $menuApiDefaultUpdate = $auth->createPermission('menuApiDefaultUpdate');
        $menuApiDefaultUpdate->description = 'Menu Api Default Update';
        $auth->add($menuApiDefaultUpdate);
        $auth->addChild($admin, $menuApiDefaultUpdate);

        $menuApiDefaultDelete = $auth->createPermission('menuApiDefaultDelete');
        $menuApiDefaultDelete->description = 'Menu Api Default Delete';
        $auth->add($menuApiDefaultDelete);
        $auth->addChild($admin, $menuApiDefaultDelete);

        $menuApiDefaultIndex = $auth->createPermission('menuApiDefaultIndex');
        $menuApiDefaultIndex->description = 'Menu Api Default Index';
        $auth->add($menuApiDefaultIndex);
        $auth->addChild($admin, $menuApiDefaultIndex);

        $menuWebDefaultIndex = $auth->createPermission('menuWebDefaultIndex');
        $menuWebDefaultIndex->description = 'Menu Web Default Index';
        $auth->add($menuWebDefaultIndex);
        $auth->addChild($admin, $menuWebDefaultIndex);

        $menuWebDefaultView = $auth->createPermission('menuWebDefaultView');
        $menuWebDefaultView->description = 'Menu Web Default View';
        $auth->add($menuWebDefaultView);
        $auth->addChild($admin, $menuWebDefaultView);

        $menuWebDefaultCreate = $auth->createPermission('menuWebDefaultCreate');
        $menuWebDefaultCreate->description = 'Menu Web Default Create';
        $auth->add($menuWebDefaultCreate);
        $auth->addChild($admin, $menuWebDefaultCreate);

        $menuWebDefaultUpdate = $auth->createPermission('menuWebDefaultUpdate');
        $menuWebDefaultUpdate->description = 'Menu Web Default Update';
        $auth->add($menuWebDefaultUpdate);
        $auth->addChild($admin, $menuWebDefaultUpdate);

        $menuWebDefaultDelete = $auth->createPermission('menuWebDefaultDelete');
        $menuWebDefaultDelete->description = 'Menu Web Default Delete';
        $auth->add($menuWebDefaultDelete);
        $auth->addChild($admin, $menuWebDefaultDelete);
        
        $menuWebItemIndex = $auth->createPermission('menuWebItemIndex');
        $menuWebItemIndex->description = 'Menu Web ItemIndex item';
        $auth->add($menuWebItemIndex);
        $auth->addChild($admin, $menuWebItemIndex);

        $menuWebItemView = $auth->createPermission('menuWebItemView');
        $menuWebItemView->description = 'Menu Web ItemView item';
        $auth->add($menuWebItemView);
        $auth->addChild($admin, $menuWebItemView);

        $menuWebItemCreate = $auth->createPermission('menuWebItemCreate');
        $menuWebItemCreate->description = 'Menu Web ItemCreate item';
        $auth->add($menuWebItemCreate);
        $auth->addChild($admin, $menuWebItemCreate);

        $menuWebItemUpdate = $auth->createPermission('menuWebItemUpdate');
        $menuWebItemUpdate->description = 'Menu Web ItemUpdate item';
        $auth->add($menuWebItemUpdate);
        $auth->addChild($admin, $menuWebItemUpdate);

        $menuWebItemDelete = $auth->createPermission('menuWebItemDelete');
        $menuWebItemDelete->description = 'Menu Web ItemDelete item';
        $auth->add($menuWebItemDelete);
        $auth->addChild($admin, $menuWebItemDelete);

        $menuWebItemRouteType = $auth->createPermission('menuWebItemRouteType');
        $menuWebItemRouteType->description = 'Menu Web ItemRouteType item';
        $auth->add($menuWebItemRouteType);
        $auth->addChild($admin, $menuWebItemRouteType);

        $menuWebItemRoute = $auth->createPermission('menuWebItemRoute');
        $menuWebItemRoute->description = 'Menu Web ItemRoute item';
        $auth->add($menuWebItemRoute);
        $auth->addChild($admin, $menuWebItemRoute);

        $menuWebItemModel = $auth->createPermission('menuWebItemModel');
        $menuWebItemModel->description = 'Menu Web ItemModel item';
        $auth->add($menuWebItemModel);
        $auth->addChild($admin, $menuWebItemModel);

        $menuWebItemSort = $auth->createPermission('menuWebItemSort');
        $menuWebItemSort->description = 'Menu Web ItemModel item';
        $auth->add($menuWebItemSort);
        $auth->addChild($admin, $menuWebItemSort);
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
        $auth->remove($auth->getPermission('menuWebItemSort'));

    }
}