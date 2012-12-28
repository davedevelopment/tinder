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

        $this['tinder.template_rendering_listener'] = $this->share(function() use ($app) {
            return new TemplateRenderingListener($app);
        });

        $this['dispatcher_class'] = "PimpleAwareEventDispatcher\PimpleAwareEventDispatcher";
        $this['dispatcher'] = $this->share($this->extend('dispatcher', function($dispatcher) use ($app) {
            $dispatcher->setContainer($app);
            $dispatcher->addSubscriberService("tinder.template_rendering_listener", "Tinder\EventListener\TemplateRenderingListener");
            return $dispatcher;
        }));
    }
}
