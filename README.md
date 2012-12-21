Tinder
======

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

$app->post("/user/{user}", function(MyApp\User $user) {
    // serious business stuff
})
->convert("user", $someKindOfUserConverter) 
->enforce(function(MyApp\User $user) use ($app) { 
    // enforce some security - can also take Security componed roles
    return $user === $app['security']->getToken()->getUser();
})
->redirect(function(MyApp\User $user) { 
    // redirect on success - ease of use needs to be much better
    return "/user/" . $user->getId() 
}) 
->present(function(array $data) { 
    // some data conversion before template
    $data['user'] = new MyApp\Presenter\User($user);
    return $data;
})
->template("user_edit.html.twig"); // use this template

```

Contributing
------------

Feel free to fork and send me pull requests, but I don't have a 1.0 release yet,
so I may change the API quite frequently. If you want to implement something
that I might easily break, please drop me an email

Copyright
---------

Copyright (c) 2012 Dave Marshall. See LICENCE for further details

