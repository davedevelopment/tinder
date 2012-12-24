Tinder
======

:fire: :fire: With Silex and Tinder, your apps will be on FIRE! :fire: :fire:

A few helpers for Silex, that help you move boundaries outside your controllers
where possible

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

Feel free to fork and send me pull requests, but I don't have a 1.0 release yet,
so I may change the API quite frequently. If you want to implement something
that I might easily break, please drop me an email

Copyright
---------

Copyright (c) 2012 Dave Marshall. See LICENCE for further details

