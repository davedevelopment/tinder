<?php

namespace Tinder;

use PimpleAwareEventDispatcher\PimpleAwareEventDispatcher;
use Silex\Application as BaseApplication;
use Silex\Provider\ServiceControllerServiceProvider;
use Tinder\Controller\ControllerResolver;
use Tinder\EventListener\ViewListener;
use Tinder\EventListener\RedirectListener;

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

        $this->register(new ServiceControllerServiceProvider());

        $this['tinder.view_listener'] = $this->share(function() use ($app) {
            return new ViewListener($app);
        });

        $this['tinder.redirect_listener'] = $this->share(function() use ($app) {
            $urlGenerator = isset($app['url_generator']) ? $app['url_generator'] : null;

            return new RedirectListener($app['routes'], $app['resolver'], $urlGenerator);
        });

        $this['dispatcher'] = $this->share($this->extend('dispatcher', function($dispatcher) use ($app) {
            $pimpleAwareDispatcher = new PimpleAwareEventDispatcher($dispatcher, $app);
            $pimpleAwareDispatcher->addSubscriberService("tinder.view_listener", "Tinder\EventListener\ViewListener");
            $pimpleAwareDispatcher->addSubscriberService("tinder.redirect_listener", "Tinder\EventListener\RedirectListener");

            return $pimpleAwareDispatcher;
        }));
    }
}
