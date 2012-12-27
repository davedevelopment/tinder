<?php

namespace Tinder;

use Silex\Application as BaseApplication;
use Tinder\Controller\ControllerResolver;

class Application extends BaseApplication
{
    public function __construct()
    {
        parent::__construct();

        $app = $this;

        $this['resolver'] = $this->share(function () use ($app) {
            return new ControllerResolver($app, $app['logger']);
        });
    }
}
