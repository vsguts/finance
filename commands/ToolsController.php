<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use yii\rbac\Permission;
use yii\db\Query;
use app\components\rbac\OwnerRule;
use app\models\User;

/**
 * Application tools
 */
class ToolsController extends Controller
{
    /**
     * Сreate(recreate) RBAC items
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
         * Roles
         */

        $roles['root'] = $auth->createRole('root');
        $roles['root']->data['system'] = true;
        foreach ($auth_data_objects as $object) {
            $roles['root']->data[$object] = ['all'];
        }
        $roles['root']->description = 'Root';

        $roles['guest'] = $auth->createRole('guest');
        $roles['guest']->data['system'] = true;
        $roles['guest']->description = 'Guest';

        /**
         * Rules
         */

        $rules['owner'] = new OwnerRule;


        /**
         * Permissions
         */
        
        // Site
        $permissions['user_manage_own'] = $auth->createPermission('user_manage_own');
        $permissions['user_manage_own']->description = 'Site::Manage own user profile';
        $permissions['user_manage_own']->ruleName = $rules['owner']->name;

        $permissions['about_page'] = $auth->createPermission('about_page');
        $permissions['about_page']->description = 'Site::About page';

        $permissions['faq_page'] = $auth->createPermission('faq_page');
        $permissions['faq_page']->description = 'Site::FAQ page';

        $permissions['contact_form'] = $auth->createPermission('contact_form');
        $permissions['contact_form']->description = 'Site::Contact form';


        // Administration
        $permissions['setting_view'] = $auth->createPermission('setting_view');
        $permissions['setting_view']->description = 'Administration::Settings view';

        $permissions['setting_manage'] = $auth->createPermission('setting_manage');
        $permissions['setting_manage']->description = 'Administration::Settings manage';

        $permissions['user_view'] = $auth->createPermission('user_view');
        $permissions['user_view']->description = 'Administration::Users view';

        $permissions['user_manage'] = $auth->createPermission('user_manage');
        $permissions['user_manage']->description = 'Administration::Users manage';

        $permissions['user_act_on_behalf'] = $auth->createPermission('user_act_on_behalf');
        $permissions['user_act_on_behalf']->description = 'Administration::Users act on behalf';

        $permissions['user_role_view'] = $auth->createPermission('user_role_view');
        $permissions['user_role_view']->description = 'Administration::User roles view';

        $permissions['user_role_manage'] = $auth->createPermission('user_role_manage');
        $permissions['user_role_manage']->description = 'Administration::User roles manage';

        // Registries

        $permissions['account_view'] = $auth->createPermission('account_view');
        $permissions['account_view']->description = 'Registries::Accounts view';

        $permissions['account_manage'] = $auth->createPermission('account_manage');
        $permissions['account_manage']->description = 'Registries::Accounts manage';

        $permissions['counterparty_view'] = $auth->createPermission('counterparty_view');
        $permissions['counterparty_view']->description = 'Registries::Counterparty view';

        $permissions['counterparty_manage'] = $auth->createPermission('counterparty_manage');
        $permissions['counterparty_manage']->description = 'Registries::Counterparty manage';

        $permissions['currency_view'] = $auth->createPermission('currency_view');
        $permissions['currency_view']->description = 'Registries::Currencies view';

        $permissions['currency_manage'] = $auth->createPermission('currency_manage');
        $permissions['currency_manage']->description = 'Registries::Currencies manage';

        $permissions['classification_view'] = $auth->createPermission('classification_view');
        $permissions['classification_view']->description = 'Registries::Classifications view';

        $permissions['classification_manage'] = $auth->createPermission('classification_manage');
        $permissions['classification_manage']->description = 'Registries::Classifications manage';

        // Transactions

        $permissions['transaction_view'] = $auth->createPermission('transaction_view');
        $permissions['transaction_view']->description = 'Transactions::View';

        $permissions['transaction_edit'] = $auth->createPermission('transaction_edit');
        $permissions['transaction_edit']->description = 'Transactions::Edit';

        $permissions['transaction_manage'] = $auth->createPermission('transaction_manage');
        $permissions['transaction_manage']->description = 'Transactions::Manage';

        $permissions['transaction_delete'] = $auth->createPermission('transaction_delete');
        $permissions['transaction_delete']->description = 'Transactions::Delete';
        

        /**
         * Saving
         */

        $added = [];
        foreach ($rules as $item) {
            $exists = (new Query)->select('name')->from('auth_rule')->where(['name' => $item->name])->one();
            if (!$exists) {
                $auth->add($item);
            }
            $added[] = $item->name;
        }
        Yii::$app->db->createCommand()->delete('auth_rule', ['not in', 'name', $added])->execute();

        $added = [];
        foreach ([$roles, $permissions] as $items) {
            foreach ($items as $item) {
                $table = 'auth_item';

                $exists = (new Query)->select('name')->from('auth_item')->where([
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
        Yii::$app->db->createCommand()->delete('auth_item', ['not in', 'type', array_keys($added)])->execute();
        foreach ($added as $type => $item_names) {
            if ($type != 1) { // Not role
                Yii::$app->db->createCommand()
                    ->delete('auth_item', ['and', ['type' => $type], ['not in', 'name', $item_names]])
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
            if (strpos($permission->name, '_own') && !$auth->hasChild($roles['guest'], $permission)) {
                $auth->addChild($roles['guest'], $permission);
            }
        }
        
        // Assign root to user.id=1
        if (!$auth->getAssignment($roles['root']->name, 1)) {
            $auth->assign($roles['root'], 1);
        }
    }

    /**
     * Getting Currency rates
     * @param timestamp $from Begin of period
     * @param timestamp $to   End of period
     */
    public function actionCurrencyRates($from = null, $to = null)
    {
        if (is_null($from)) {
            $from = time();
        }
        if (is_null($to)) {
            $to = time();
        }
        
        $rates = Yii::$app->currency->getPeriodRates($from, $to);
        
        pd($rates);
    }
}
