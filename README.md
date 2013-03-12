Tinder
======

:fire: :fire: With Silex and Tinder, your apps will be on fire :fire: :fire:

[![Build Status](https://travis-ci.org/davedevelopment/tinder.png)](https://travis-ci.org/davedevelopment/tinder)

A few helpers for Silex, that help you move boundaries outside your controllers
where possible. It's a little hard to explain why you might want that, but
eventually I'd like to have an example application that can show some of the
benefits. 

At this stage, Tinder is completely backwards-compatible with
Silex, so you should be able to instantiate or extend `Tinder\Application`,
rather than `Silex\Application` and your existing functionality will still work.

## Table Of Contents ##

* [Controllers as Services](#controllers-as-services)
* [Argument Injection](#argument-injection)
* [Template Rendering](#template-rendering)
* [Redirects](#redirects)
* [Event Listeners as Services](#event-listeners-as-services)
* [README Driven Development](#readme-driven-development)
* [Contributing](#contributing)
* [Copyright](#copyright)

## Controllers as Services ##

**UPDATE** - This is now available as the
[ServiceControllerServiceProvider](http://silex.sensiolabs.org/doc/providers/service_controller.html)
the Silex Core, Tinder enables it by default.

## Argument Injection ##

Tinder extends the default controller resolver to allow injecting HTTP GET or POST
vars directly as controller arguments.

``` php
<?php

// an array called query will be GET vars
$app->get("/search", function (array $query) {
});

// an array called params will be POST vars
$app->post("/blog/posts", function (array $params) {
});

```

*Why?* This means your controllers can be dependent on an arbitrary array of query vars or
parameters, rather than a `Symfony\Component\HttpFoundation\Request`. Want to
share controller code between the CLI and Web interfaces? 

## Template Rendering ##

Tinder adds a simple route helper that allows you to return an array of context
variables from your controllers and have them rendered by a twig template
defined on the route. An optional second argument allows you to manipulate the
returned data before being passed to the template, allowing for presenters or
ViewModels and the like.

``` php
<?php

$app->get("/dave", function() {
    return array(
        "rating" => rand(1,10),
    );
})
->template("dave.html.twig", function($data) {
    $data['rating'] = 10;
    return $data;
});

```

*Why?* Why should your controller be responsible for rendering a template? 

Redirects
=========

Redirecting to a URL after a successful action
(e.g. [POST/REDIRECT/GET](http://en.wikipedia.org/wiki/Post/Redirect/Get))
doesn't have to be the concern of your controller/use case/interactor, tinder
provides a simple DSL to tell it to send a redirect response if the controller returns null.

``` php

$app->post("/users/{id}", function() { return; })
    ->redirect("/users");

```

If you're using the `UrlGeneratorServiceProvider`, you send a string id and a
callable, which must return the params for the url generator. The callable can
use the same name and type hinting semantics as a regular controller.

``` php

$app->get("/users/{id}", function($id) { })
    ->bind("get_user");

$app->post("/users/{id}", function($id) { return; })
    ->redirect("get_user", function($id) { return ["id" => $id]; });

```

Something similar can also be achieved if you're not using the generator, by
having a callable generate the url.

``` php

$app->post("/users/{id}", function($id) { return; })
    ->redirect(function($id) { return "/users/$id"; });

```


Event Listeners as Services
===========================

Much like the controllers, Tinder includes a `PimpleAwareEventDispatcher`,
allowing you to configure services as event listeners or subscribers, so that
they are lazy loaded.

``` php
<?php

$app['dispatcher']->addListenerService('some.event', array('some_service_name', 'someMethod'));
$app['dispatcher']->addSubscriberService('some_other_service', 'The\Fully\Qualified\ClassName\Of\That\Service');

```

Why? Performance. If you fully adopt the event dispatcher, your listeners are
likely to be many, which in turn are likely to have many dependencies. This
method saves loading them until they're actually needed.

Readme Driven Development
=========================

Most of this isn't implemented yet, but here's the kind of thing I'd like to get
to, if it's even possible

``` php
<?php

require "vendor/autoload.php"

$app = new Tinder\Application;

$app->post("/user/{user}", "my_controller_as_service:post")
    ->convert("user", $someKindOfUserConverter) 
    ->enforce("ROLE_ADMIN") // only admins can do this, could be a closure with true/false return or throw
    ->redirect("get_user", function(User $user) {
        // on success, go to the get_user url (no idea how I'll do the params yet)
        // params can be string as url or route name and/or closure for
        // pre-processing, returning an url if a string param is not present
    }) 
    ->template("user_edit.html.twig", function(array $data) { 
        // use this template and do some optional data conversion beforehand 
        $data['user'] = new MyApp\Presenter\User($user);
        return $data;
    });

$app->get("/some-page", function() {})
    ->template("some_page.html.twig")
    ->cache(["expires" => "tomorrow"]); // http cache until tomorrow

```

Contributing
------------

Get in touch before hacking on anything, I've no idea what I might be doing, the
whole library may have changed since my last push :)

Copyright
---------

Copyright (c) 2012 Dave Marshall. See LICENCE for further details

