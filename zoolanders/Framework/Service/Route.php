<?php

namespace Zoolanders\Service;

class Route extends Service
{
    /**
     *  Setup the custom routers for each application
     */
    public function setupRouters()
    {
        $this->container->zoo->getApp()->event->dispatcher->connect('application:sefparseroute', function ($event) {

            $app_id = $this->app->request->getInt('app_id', null);
            $app = $this->app->table->application->get($app_id);

            // check if was loaded
            if (!$app) return;

            $group = $app->getGroup();
            if ($router = $this->app->path->path("applications:$group/router.php")) {

                require_once $router;

                $class = 'ZLRouter' . ucfirst($group);
                $routerClass = new $class;
                $routerClass->parseRoute($event);
            }
        });
    }
}