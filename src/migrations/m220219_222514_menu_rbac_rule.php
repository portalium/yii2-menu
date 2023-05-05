<?php

use portalium\db\Migration;
use portalium\menu\rbac\OwnRule;


class m220219_222514_menu_rbac_rule extends Migration
{
    public function up()
    {
        $auth = Yii::$app->authManager;
        $rule = new OwnRule();
        $auth->add($rule);
        $role = Yii::$app->setting->getValue('site::admin_role');
        $admin = (isset($role) && $role != '') ? $auth->getRole($role) : $auth->getRole('admin');
        $permissionsName = [
            'menuApiDefaultViewOwn',
            'menuApiDefaultUpdateOwn',
            'menuApiDefaultDeleteOwn',
            'menuWebDefaultViewOwn',
            'menuWebDefaultUpdateOwn',
            'menuWebDefaultDeleteOwn',
            'menuWebItemViewOwn',
            'menuWebItemUpdateOwn',
            'menuWebItemDeleteOwn',
        ];

        foreach ($permissionsName as $permissionName) {
            $permission = $auth->createPermission($permissionName);
            $permission->description = $permissionName;
            $permission->ruleName = $rule->name;
            $auth->add($permission);
            $auth->addChild($admin, $permission);
            $childPermission = $auth->getPermission(str_replace('Own', '', $permissionName));
            $auth->addChild($permission, $childPermission);
        }

        $permissionsName = [
            'menuApiDefaultIndexOwn',
            'menuWebDefaultIndexOwn',
            'menuWebItemIndexOwn',
        ];

        foreach ($permissionsName as $permissionName) {
            $permission = $auth->createPermission($permissionName);
            $permission->description = $permissionName;
            $auth->add($permission);
            $auth->addChild($admin, $permission);
        }

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