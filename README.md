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
* [README Driven Development](#readme-driven-development)
* [Contributing](#contributing)
* [Copyright](#copyright)

## Controllers as Services ##

Tinder extends the default controller resolver to allow you to register pimple
services as controllers, using the syntax `service_name:method`.

``` php
<?php

require "vendor/autoload.php";

$app = new Tinder\Application;
$app['config'] = ["dave" => "is ace"];
$app['my_controller'] = function() use ($app) {
    return new MyController($app['config']);
};

$app->get("/dave", "my_controller:daveAction");

```

*Why?* Controllers as Services are a nice way to conform to several OOP
principles, allowing you to organise your controllers, clearly define their
dependencies and easily spec/test them.

## Argument Injection ##

Tinder extends the default controller resolver to allow injecting HTTP GET or POST
vars directly as controller arguments.

** @TODO might be better passing an object here? e.g. `function (Tinder\ParameterBag $params)` **

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
    return array("
        "rating" => rand(1,10),
    );
})
->template("dave.html.twig", function($data) {
    $data['rating'] = 10;
    return $data;
});

```

*Why?* Why should your controller be responsible for rendering a template? 

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

