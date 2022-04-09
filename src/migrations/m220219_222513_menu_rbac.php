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

        $menuBackendDefaultIndex = $auth->createPermission('menuBackendDefaultIndex');
        $menuBackendDefaultIndex->description = 'View menu';
        $auth->add($menuBackendDefaultIndex);
        $auth->addChild($admin, $menuBackendDefaultIndex);

        $menuBackendDefaultView = $auth->createPermission('menuBackendDefaultView');
        $menuBackendDefaultView->description = 'View menu';
        $auth->add($menuBackendDefaultView);
        $auth->addChild($admin, $menuBackendDefaultView);

        $menuBackendDefaultCreate = $auth->createPermission('menuBackendDefaultCreate');
        $menuBackendDefaultCreate->description = 'Create menu';
        $auth->add($menuBackendDefaultCreate);
        $auth->addChild($admin, $menuBackendDefaultCreate);

        $menuBackendDefaultUpdate = $auth->createPermission('menuBackendDefaultUpdate');
        $menuBackendDefaultUpdate->description = 'Update menu';
        $auth->add($menuBackendDefaultUpdate);
        $auth->addChild($admin, $menuBackendDefaultUpdate);

        $menuBackendDefaultDelete = $auth->createPermission('menuBackendDefaultDelete');
        $menuBackendDefaultDelete->description = 'Delete menu';
        $auth->add($menuBackendDefaultDelete);
        $auth->addChild($admin, $menuBackendDefaultDelete);
        
        $menuBackendItemIndex = $auth->createPermission('menuBackendItemIndex');
        $menuBackendItemIndex->description = 'View menu item';
        $auth->add($menuBackendItemIndex);
        $auth->addChild($admin, $menuBackendItemIndex);

        $menuBackendItemView = $auth->createPermission('menuBackendItemView');
        $menuBackendItemView->description = 'View menu item';
        $auth->add($menuBackendItemView);
        $auth->addChild($admin, $menuBackendItemView);

        $menuBackendItemCreate = $auth->createPermission('menuBackendItemCreate');
        $menuBackendItemCreate->description = 'Create menu item';
        $auth->add($menuBackendItemCreate);
        $auth->addChild($admin, $menuBackendItemCreate);

        $menuBackendItemUpdate = $auth->createPermission('menuBackendItemUpdate');
        $menuBackendItemUpdate->description = 'Update menu item';
        $auth->add($menuBackendItemUpdate);
        $auth->addChild($admin, $menuBackendItemUpdate);

        $menuBackendItemDelete = $auth->createPermission('menuBackendItemDelete');
        $menuBackendItemDelete->description = 'Delete menu item';
        $auth->add($menuBackendItemDelete);
        $auth->addChild($admin, $menuBackendItemDelete);

        $menuBackendItemRouteType = $auth->createPermission('menuBackendItemRouteType');
        $menuBackendItemRouteType->description = 'View menu item';
        $auth->add($menuBackendItemRouteType);
        $auth->addChild($admin, $menuBackendItemRouteType);

        $menuBackendItemRoute = $auth->createPermission('menuBackendItemRoute');
        $menuBackendItemRoute->description = 'View menu item';
        $auth->add($menuBackendItemRoute);
        $auth->addChild($admin, $menuBackendItemRoute);

        $menuBackendItemModel = $auth->createPermission('menuBackendItemModel');
        $menuBackendItemModel->description = 'View menu item';
        $auth->add($menuBackendItemModel);
        $auth->addChild($admin, $menuBackendItemModel);

    }

    public function down()
    {
        $auth = Yii::$app->authManager;

        $auth->remove($auth->getPermission('menuApiDefaultView'));
        $auth->remove($auth->getPermission('menuApiDefaultCreate'));
        $auth->remove($auth->getPermission('menuApiDefaultUpdate'));
        $auth->remove($auth->getPermission('menuApiDefaultDelete'));
        $auth->remove($auth->getPermission('menuApiDefaultIndex'));
        $auth->remove($auth->getPermission('menuBackendDefaultIndex'));
        $auth->remove($auth->getPermission('menuBackendDefaultView'));
        $auth->remove($auth->getPermission('menuBackendDefaultCreate'));
        $auth->remove($auth->getPermission('menuBackendDefaultUpdate'));
        $auth->remove($auth->getPermission('menuBackendDefaultDelete'));
        $auth->remove($auth->getPermission('menuBackendItemIndex'));
        $auth->remove($auth->getPermission('menuBackendItemView'));
        $auth->remove($auth->getPermission('menuBackendItemCreate'));
        $auth->remove($auth->getPermission('menuBackendItemUpdate'));
        $auth->remove($auth->getPermission('menuBackendItemDelete'));
        $auth->remove($auth->getPermission('menuBackendItemRouteType'));
        $auth->remove($auth->getPermission('menuBackendItemRoute'));
        $auth->remove($auth->getPermission('menuBackendItemModel'));

    }
}