<?php

namespace app\components\rbac;

use Yii;
use yii\db\Query;
use yii\rbac\Item;

class DbManager extends \yii\rbac\DbManager
{
    const ALL_OBJECTS = 'all';

    protected $assignments = [];

    protected $userObjects = [];

    /**
     * Getting roles list. Applying for checkboxes
     *
     * @param  bool  $skipDefault Skip roles list
     * @return array
     */
    public function getRolesList($skipDefault = false)
    {
        $defaultRoleIds = Yii::$app->authManager->defaultRoles;
        $roles = [];
        foreach ($this->getRoles() as $role) {
            if ($skipDefault && in_array($role->name, $defaultRoleIds)) {
                continue;
            }
            $roles[$role->name] = $role->description;
        }
        asort($roles);

        return $roles;
    }

    /**
     * @inheritdoc
     */
    public function updateItem($name, $item)
    {
        return parent::updateItem($name, $item);
    }

    /**
     * @inheritdoc
     */
    public function getItem($name)
    {
        return parent::getItem($name);
    }

    /**
     * @inheritdoc
     */
    public function getAssignments($userId)
    {
        if (!isset($this->assignments[$userId])) {
            $this->assignments[$userId] = parent::getAssignments($userId);
        }
        return $this->assignments[$userId];
    }

    /**
     * Gets all user available items
     * @param  int $user_id User ID
     * @return array
     */
    public function getAllUserItemNames($user_id)
    {
        $items = [];

        $children = $this->getChildrenList();
        foreach ($this->getRolesByUser($user_id) as $role) {
            $items = array_merge($items, $this->getItemsRecursive($role->name, $children));
        }
        return array_unique($items);
    }

    protected function getItemsRecursive($item, &$children)
    {
        $result = [$item];
        if (!empty($children[$item])) {
            foreach ($children[$item] as $child) {
                $result = array_merge($result, $this->getItemsRecursive($child, $children));
            }
        }
        return $result;
    }


    /**
     * Application
     */

    public function getDataObjects()
    {
        return [
            'accounts',
        ];
    }

    public function getUserObjects($object_name)
    {
        if (!$this->userObjects) {
            $data = array_fill_keys($this->getDataObjects(), []);
            $role_names = $this->getAllUserItemNames(Yii::$app->user->id);
            $query = (new Query)
                ->from($this->itemTable)
                ->where([
                    'name' => $role_names,
                    'type' => Item::TYPE_ROLE,
                ]);

            foreach ($query->all($this->db) as $row) {
                $role = $this->populateItem($row);
                if ($role->data) {
                    foreach ($role->data as $name => $object) {
                        if (is_array($object)) {
                            if (isset($data[$name])) {
                                $data[$name] = array_merge($data[$name], $object);
                            } else {
                                $data[$name] = $object;
                            }
                        }
                    }
                }
            }
            $this->userObjects = $data;
        }

        if (array_search('all', $this->userObjects[$object_name]) !== false) {
            return self::ALL_OBJECTS;
        }

        return $this->userObjects[$object_name];
    }

    public function getRolesByPermission($permission)
    {
        $parents = [];
        foreach ($this->getChildrenList() as $parent => $children) {
            foreach ($children as $child) {
                $parents[$child][] = $parent;
            }
        }
        return $this->getItemsRecursive($permission, $parents);
    }

}
