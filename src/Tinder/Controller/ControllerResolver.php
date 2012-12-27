<?php

namespace Tinder\Controller;

use Silex\ControllerResolver as BaseControllerResolver;
use Symfony\Component\HttpFoundation\Request;

class ControllerResolver extends BaseControllerResolver
{
    protected function createController($controller)
    {
        if (false !== strpos($controller, '::')) {
            return parent::createController($controller);
        }

        if (false === strpos($controller, ':')) {
            throw new \LogicException(sprintf('Unable to parse the controller name "%s".', $controller));
        }

        list($service, $method) = explode(':', $controller, 2);

        if (!isset($this->app[$service])) {
            throw new \InvalidArgumentException(sprintf('Service "%s" does not exist.', $service));
        }

        return array($this->app[$service], $method);
    }

    protected function doGetArguments(Request $request, $controller, array $parameters) {

        foreach ($parameters as $param) {
            if ($param->isArray() && $param->getName() == 'params') {
                $request->attributes->set('params', $request->request->all());
                break;
            }
            if ($param->isArray() && $param->getName() == 'query') {
                $request->attributes->set('query', $request->query->all());
                break;
            }
        }

        return parent::doGetArguments($request, $controller, $parameters);
    }
}
