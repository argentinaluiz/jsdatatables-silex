<?php

namespace JSDataTables\Doctrine\ORM\Query\Expr;

use JSDataTables\Entity\AjaxParams;
use JSDataTables\Entity\ConfigDt;

class Select {

    private static $point = '.';

    /**
     * @return string
     */
    public static function getSelect(ConfigDt $dtConfig, AjaxParams $ajaxParams) {
        $selectNew = [];
        $partials = [];
        foreach ($ajaxParams->getColumns() as $column) {
            $dtColumn = $dtConfig->getColumn($column->getData());
            //$column->isSearchable() && $dtColumn
            if ($dtColumn) {
                $field = $dtColumn->getServer()->getField();
                $name = $field['name'];
                $alias = $field['alias'];
                if (isset($field['partial'])) {
                    $partials[$alias][] = $name;
                } else {
                    $selectNew[] = $alias .
                            self::$point .
                            $name .
                            (isset($field['alias_name']) ? " AS {$field['alias_name']}" : '');
                }
            }
        }
        foreach ($partials as $key => $value) {
            $selectNew[] = "partial $key.{" . implode(',', $value) . "}";
        }
        return implode(',', $selectNew);
    }

}
