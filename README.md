Tinder
======

:fire: :fire: With Silex and Tinder, your apps will be on fire :fire: :fire:

[![Build Status](https://travis-ci.org/davedevelopment/tinder.png)](https://travis-ci.org/davedevelopment/tinder)

A few helpers for Silex, that help you move boundaries outside your controllers
where possible

## Table Of Contents ##

* [Controllers as Services](#controllers-as-services)
* [Argument Injection](#argument-injection)
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

## Argument Injection ##

Tinder extends Silex's Controller Resolver to allow injecting HTTP GET or POST
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

