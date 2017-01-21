<?php

namespace app\commands;

use app\components\rbac\LoggedInRule;
use app\components\rbac\LoggedOutRule;
use app\components\rbac\OwnerRule;
use Yii;
use yii\console\Controller;
use yii\db\Query;
use yii\rbac\Item;

/**
 * Application tools
 */
class ToolsController extends Controller
{
    /**
     * Upgrade/Сreate RBAC items
     *
     * @param bool $reset Reset all existing roles, permissions and links
     */
    public function actionRbac($reset = false)
    {
        $auth = Yii::$app->authManager;
        
        $auth_data_objects = $auth->getDataObjects();

        if ($reset) {
            $auth->removeAll();
        }

        $roles = $rules = $permissions = [];


        /**
         * Rules
         */

        $rules['owner'] = new OwnerRule;

        $rules['logged_in'] = new LoggedInRule;

        $rules['logged_out'] = new LoggedOutRule;


        /**
         * Roles
         */

        $roles['guest'] = $auth->createRole('guest');
        $roles['guest']->data['system'] = true;
        $roles['guest']->description = 'Guest';
        $roles['guest']->ruleName = $rules['logged_out']->name;

        $roles['authorized'] = $auth->createRole('authorized');
        $roles['authorized']->data['system'] = true;
        $roles['authorized']->description = 'Authorized';
        $roles['authorized']->ruleName = $rules['logged_in']->name;

        $roles['root'] = $auth->createRole('root');
        foreach ($auth_data_objects as $object) {
            $roles['root']->data[$object] = ['all'];
        }
        $roles['root']->description = 'Root';


        /**
         * Permissions
         */

        // Help

        $permissions['about_page'] = $auth->createPermission('about_page');
        $permissions['about_page']->description = 'Administration::Site::About page';

        $permissions['faq_page'] = $auth->createPermission('faq_page');
        $permissions['faq_page']->description = 'Administration::Site::FAQ page';

        $permissions['contact_form'] = $auth->createPermission('contact_form');
        $permissions['contact_form']->description = 'Administration::Site::Contact form';

        // Administration

        $permissions['setting_view'] = $auth->createPermission('setting_view');
        $permissions['setting_view']->description = 'Administration::Settings::View';

        $permissions['setting_manage'] = $auth->createPermission('setting_manage');
        $permissions['setting_manage']->description = 'Administration::Settings::Manage';

        $permissions['user_manage_own'] = $auth->createPermission('user_manage_own');
        $permissions['user_manage_own']->description = 'Administration::Users::Manage own profile';
        $permissions['user_manage_own']->ruleName = $rules['owner']->name;

        $permissions['user_view'] = $auth->createPermission('user_view');
        $permissions['user_view']->description = 'Administration::Users::View';

        $permissions['user_manage'] = $auth->createPermission('user_manage');
        $permissions['user_manage']->description = 'Administration::Users::Manage';

        $permissions['user_act_on_behalf'] = $auth->createPermission('user_act_on_behalf');
        $permissions['user_act_on_behalf']->description = 'Administration::Users::Act on behalf';

        $permissions['user_role_view'] = $auth->createPermission('user_role_view');
        $permissions['user_role_view']->description = 'Administration::Users::Roles view';

        $permissions['user_role_manage'] = $auth->createPermission('user_role_manage');
        $permissions['user_role_manage']->description = 'Administration::Users::Roles manage';

        // Registries

        $permissions['account_view'] = $auth->createPermission('account_view');
        $permissions['account_view']->description = 'Registries::Accounts::View';

        $permissions['account_manage'] = $auth->createPermission('account_manage');
        $permissions['account_manage']->description = 'Registries::Accounts::Manage';

        $permissions['counterparty_view'] = $auth->createPermission('counterparty_view');
        $permissions['counterparty_view']->description = 'Registries::Counterparty::View';

        $permissions['counterparty_manage'] = $auth->createPermission('counterparty_manage');
        $permissions['counterparty_manage']->description = 'Registries::Counterparty::Manage';

        $permissions['currency_view'] = $auth->createPermission('currency_view');
        $permissions['currency_view']->description = 'Registries::Currencies::View';

        $permissions['currency_manage'] = $auth->createPermission('currency_manage');
        $permissions['currency_manage']->description = 'Registries::Currencies::Manage';

        $permissions['classification_view'] = $auth->createPermission('classification_view');
        $permissions['classification_view']->description = 'Registries::Classifications::View';

        $permissions['classification_manage'] = $auth->createPermission('classification_manage');
        $permissions['classification_manage']->description = 'Registries::Classifications::Manage';

        // Transactions

        $permissions['transaction_view'] = $auth->createPermission('transaction_view');
        $permissions['transaction_view']->description = 'Transactions::Transactions::View';

        $permissions['transaction_edit'] = $auth->createPermission('transaction_edit');
        $permissions['transaction_edit']->description = 'Transactions::Transactions::Edit';

        $permissions['transaction_manage'] = $auth->createPermission('transaction_manage');
        $permissions['transaction_manage']->description = 'Transactions::Transactions::Manage';

        $permissions['transaction_delete'] = $auth->createPermission('transaction_delete');
        $permissions['transaction_delete']->description = 'Transactions::Transactions::Delete';


        /**
         * Saving
         */

        Yii::$app->db->createCommand()->delete($auth->ruleTable)->execute();
        foreach ($rules as $item) {
            $auth->add($item);
        }

        $added = [];
        foreach ([$roles, $permissions] as $items) {
            foreach ($items as $item) {
                $exists = (new Query)->select('name')->from($auth->itemTable)->where([
                    'name' => $item->name,
                    'type' => $item->type,
                ])->one();

                if (!$exists) {
                    $auth->add($item);
                } else {
                    $_item = $auth->getItem($item->name);
                    foreach ($auth_data_objects as $object) {
                        if (isset($_item->data[$object])) {
                            $item->data[$object] = $_item->data[$object];
                        }
                    }
                    $auth->updateItem($item->name, $item);
                }
                $added[$item->type][] = $item->name;
            }
        }
        Yii::$app->db->createCommand()->delete($auth->itemTable, ['not in', 'type', array_keys($added)])->execute();
        foreach ($added as $type => $item_names) {
            if ($type == Item::TYPE_PERMISSION) { // Permission
                Yii::$app->db->createCommand()
                    ->delete($auth->itemTable, ['and', ['type' => $type], ['not in', 'name', $item_names]])
                    ->execute();
            }
        }

        /**
         * Root relations
         */

        foreach ($permissions as $permission) {
            // All permissions to root
            if (!$auth->hasChild($roles['root'], $permission)) {
                $auth->addChild($roles['root'], $permission);
            }
            // Own permissions to guest
            // if (strpos($permission->name, '_own') && !$auth->hasChild($roles['guest'], $permission)) {
            //     $auth->addChild($roles['guest'], $permission);
            // }
        }

        // Assign root to user.id=1
        if (!$auth->getAssignment($roles['root']->name, 1)) {
            $auth->assign($roles['root'], 1);
        }
    }

}
