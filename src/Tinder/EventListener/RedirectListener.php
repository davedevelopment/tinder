<?php

namespace Tinder\EventListener;

use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
use Symfony\Component\Routing\RouteCollection;

/**
 * This listens to controllers responses for a null return value, if found, it
 * will look for a _redirect attribute against the route and attempt to use it
 * to send a RedirectResponse
 *
 */
class RedirectListener implements EventSubscriberInterface
{
    protected $resolver;
    protected $urlGenerator;

    protected $enabled = true;

    public function __construct(RouteCollection $routes, ControllerResolverInterface $resolver, UrlGeneratorInterface $urlGenerator = null)
    {
        $this->routes = $routes;
        $this->resolver = $resolver;
        $this->urlGenerator = $urlGenerator;
    }

    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        if (!$this->enabled) {
            return;
        }

        $response = $event->getControllerResult();
        if ($response !== null) {
            return;
        }

        $request = $event->getRequest();
        $routeName = $request->attributes->get('_route');
        if (!$route = $this->routes->get($routeName)) {
            return;
        }

        if (!$args = $route->getOption('_redirect')) {
            return;
        }

        $uri = isset($args['uri']) ? $args['uri'] : null;
        if (isset($args['callable'])) {
            $arguments = $this->resolver->getArguments($request, $args['callable']);
            $resolved = call_user_func_array($args['callable'], $arguments);

            if (null == $uri) {
                $uri = $resolved;
            } else {
                if (null === $this->urlGenerator) {
                    throw new \InvalidArgumentException(
                        "The UrlGeneratorServiceProvider must be registered to use named routes"
                    );
                }

                $uri = $this->urlGenerator->generate($uri, $resolved);
            }
        }

        if (!is_string($uri)) {
            throw new \InvalidArgumentException(
                "Could not resolve uri for redirect"
            );
        }

        $event->setResponse(new RedirectResponse($uri));
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $this->enabled = false;
    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::VIEW => array('onKernelView', -10),
            KernelEvents::EXCEPTION => array('onKernelException', 10),
        );
    }
}
