<?php

namespace Tinder\EventListener;

use Silex\Application;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * If a route returns an array and has set a template, we render it here
 */
class TemplateRenderingListener implements EventSubscriberInterface
{
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * @param GetResponseForControllerResultEvent $event The event to handle
     */
    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        /**
         * Anytime we don't find what we're looking for, return and let other 
         * listeners handle things
         */
        $response = $event->getControllerResult();
        if (!is_array($response)) {
            return;
        }

        $request = $event->getRequest();
        $routeName = $request->attributes->get('_route');
        if (!$route = $this->app['routes']->get($routeName)) {
            return;
        }

        if (!$args = $route->getOption('_template')) {
            return; 
        }

        list($template, $callback) = $args;

        if ($callback) {
            $response = $callback($response);
        }

        $output = $this->render($template, $response);
        $event->setResponse(new Response($output));
    }

    protected function render($template, array $context) {
        // deal with other engines here/make configurable
        return $this->app['twig']->render($template, $context);
    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::VIEW => array('onKernelView', -10),
        );
    }
}
