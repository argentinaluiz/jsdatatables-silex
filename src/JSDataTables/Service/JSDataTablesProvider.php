<?php

namespace JSDataTables\Service;

use Silex\Application;
use Silex\ServiceProviderInterface;

class JSDataTablesProvider implements ServiceProviderInterface {

    public function boot(Application $app) {
        
    }

    public function register(Application $app) {
        $app['js.datatables_manager'] = $app->share(function() use ($app)
        {
            $object = new JSDataTablesManager();
            $object->setApp($app);
            return $object;
        });

        $app['js.datatables.field_filter'] = $app->share(function()
        {
            return new FieldFilterManager(new \Zend\Filter\FilterChain());
        });

        $app['js.datatables.list_clause'] = $app->share(function() use ($app)
        {
            return new ListClause($app['js.datatables.field_filter']);
        });
    }

}
