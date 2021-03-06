<?php

namespace app\models\query;

use app\models\AuthItem;

class AuthItemChildQuery extends ActiveQuery
{
    private static $childSchema;

    private static $parentSchema;

    public function getChildSchema()
    {
        if (!isset(self::$childSchema)) {
            $children = [];
            foreach ($this->asArray()->all() as $row) {
                $children[$row['parent']][] = $row['child'];
            }
            self::$childSchema = $children;
        }
        return self::$childSchema;
    }

    public function getParentSchema()
    {
        if (!self::$parentSchema) {
            $parents = [];
            foreach ($this->getChildSchema() as $parent => $children) {
                foreach ($children as $child) {
                    $parents[$child][] = $parent;
                }
            }
            self::$parentSchema = $parents;
        }
        return self::$parentSchema;
    }

    /**
     * @param string $itemName
     * @param bool|string $recursive bool or 'inherited'
     * @param string $type
     * @param bool $reverse
     * @return array
     */
    public function getLinkItemNames($itemName, $recursive = false, $type = null, $reverse = false)
    {
        if (!$reverse) {
            $schema = $this->getChildSchema();
        } else {
            $schema = $this->getParentSchema();
        }

        $types = AuthItem::find()->getItemTypes();

        $result = [];

        if (!empty($schema[$itemName])) {
            foreach ($schema[$itemName] as $child) {
                if (
                    (!$type || $types[$child] == $type)
                    && $recursive !== 'inherited'
                ) {
                    $result[] = $child;
                }
                if ($recursive) {
                    $result = array_merge($result, $this->getLinkItemNames($child, true, $type, $reverse));
                }
            }
        }

        return array_unique($result);
    }

}
