<?php

namespace JSDataTables\Service;

use Silex\Application;

class JSDataTablesManager implements ServiceProviderInterface {

    private $app;

    public function app($id) {
        return $this->app[$id];
    }

    public function setApp(Application $app) {
        $this->app = $app;
        return $this;
    }

    public function getDt($name) {
        $dataTablesConfig = $this->app('js_datatables');
        if (array_key_exists($name, $dataTablesConfig))
        {
            $dtConfig = $this->getDtConfig($name);
            if (isset($dtConfig['is_service']))
            {
                return $this->app($dtConfig['service_name']);
            } else
            {
                if (isset($dtConfig['class_dt']))
                {
                    $instance = new $dtConfig['class_dt'];
                    return $this->injectDependencies($instance, $this->getDtConfig($name));
                } else
                {
                    return $this->injectDependencies(new JSDataTables(), $this->getDtConfig($name));
                }
            }
        } else
        {
            throw new \InvalidArgumentException(sprintf('Invalid DataTables %s', $name));
        }
    }

    public function injectDependencies(JSDataTables $dataTable, $dtConfig) {
        return $dataTable->setDtArrayConfig($dtConfig)
                        ->setEntityManager($this->app('orm.em'))
                        ->setListClause($this->app('js.datatables.list_clause'))
                        ->setParams($this->app('request')->query->all());
    }

    public function getDtConfig($name) {
        return $this->app(['js_datatables'])[$name]['dt_config'];
    }

}
