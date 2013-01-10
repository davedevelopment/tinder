<?php

namespace Tinder\Controller;

use Silex\ControllerResolver as BaseControllerResolver;
use Symfony\Component\HttpFoundation\Request;

class ControllerResolver extends BaseControllerResolver
{
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
