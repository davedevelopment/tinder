<?php

namespace Tinder;

use Silex\Route as BaseRoute;

class Route extends BaseRoute
{
    /**
     * Render this template with the return value (array) of the controller
     *
     * @param  string  $path    - Path to the template
     * @param  Closure $closure - Optional closure for pre-processing view context
     * @return Route
     */
    public function template($path, $closure = null)
    {
        $this->setOption('_template', array($path, $closure));

        return $this;
    }

    public function redirect()
    {
        $args = func_get_args();

        $uri = null;
        $callable = null;

        while ($arg = array_shift($args)) {
            if (is_string($arg)) {
                $uri = $arg;
            } elseif (is_callable($arg)) {
                $callable = $arg;
            } else {
                throw new \InvalidArgumentException(
                    "The redirect method expects either a string, a callable, or a string and a callable, ".gettype($arg)." given"
                );
            }
        }

        $this->setOption('_redirect', array(
            'uri' => $uri,
            'callable' => $callable,
        ));

        return $this;
    }
}
