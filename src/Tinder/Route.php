<?php

namespace Tinder;

use Silex\Route as BaseRoute;

class Route extends BaseRoute
{
    /**
     * Render this template with the return value (array) of the controller
     *
     * @param string  $path    - Path to the template
     * @param Closure $closure - Optional closure for pre-processing view context
     * @return Route
     */
    public function template($path, $closure = null)
    {
        $this->setOption('_template', array($path, $closure));
        return $this;
    }
}
