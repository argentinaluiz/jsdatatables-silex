<?php

namespace JSDataTables\Service;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class JSDataTablesManager {

    private $app;

    public function app($id) {
        return $this->app[$id];
    }

    public function setApp(Application $app) {
        $this->app = $app;
        return $this;
    }

    public function getDt($name, Request $request) {
        $dataTablesConfig = $this->app('js_datatables.config');
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
                    return $this->injectDependencies($instance, $this->getDtConfig($name), $request);
                } else
                {
                    return $this->injectDependencies(new JSDataTables(), $this->getDtConfig($name), $request);
                }
            }
        } else
        {
            throw new \InvalidArgumentException(sprintf('Invalid DataTables %s', $name));
        }
    }

    public function injectDependencies(JSDataTables $dataTable, $dtConfig, Request $request) {
        return $dataTable->setDtArrayConfig($dtConfig)
                        ->setEntityManager($this->app('orm.em'))
                        ->setListClause($this->app('js.datatables.list_clause'))
                        ->setParams($request->query->all())
                        ->setValidator($this->app('validator'));
    }

    public function getDtConfig($name) {
        return $this->app('js_datatables.config')[$name]['dt_config'];
    }

}
