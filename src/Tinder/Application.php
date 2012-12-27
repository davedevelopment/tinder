<?php

namespace Tinder;

use Silex\Application as BaseApplication;
use Tinder\Controller\ControllerResolver;
use Tinder\EventListener\TemplateRenderingListener;

class Application extends BaseApplication
{
    public function __construct()
    {
        parent::__construct();

        $app = $this;

        $this['route_class'] = 'Tinder\Route';

        $this['resolver'] = $this->share(function () use ($app) {
            return new ControllerResolver($app, $app['logger']);
        });

        $this['dispatcher'] = $this->share($this->extend('dispatcher', function($dispatcher) use ($app) {
            $dispatcher->addSubscriber(new TemplateRenderingListener($app));
            return $dispatcher;
        }));
    }
}
