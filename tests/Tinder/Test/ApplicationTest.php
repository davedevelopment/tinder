<?php

namespace Tinder\Test;

use Silex\Tests\ApplicationTest as BaseApplicationTest;
use Symfony\Component\HttpFoundation\Request;
use Tinder\Application;

/**
 * Basic integration tests in here, may get split out at some point
 */
class ApplicationTest extends BaseApplicationTest
{
    /**
     * @test
     */
    public function shouldInjectGetVars()
    {
        $request = Request::create('/', "GET", array("dave" => "is ace", "tinder" => "is amazing"));
        $app = new Application();
        $app->get('/', function (array $query) use ($request) {
            return json_encode($query);
        });

        $this->assertEquals('{"dave":"is ace","tinder":"is amazing"}', $app->handle($request)->getContent());
    }

    /**
     * @test
     */
    public function shouldInjectPostVars()
    {
        $request = Request::create('/', "POST", array("dave" => "is ace", "tinder" => "is amazing"));
        $app = new Application();
        $app->post('/', function (array $params) use ($request) {
            return json_encode($params);
        });

        $this->assertEquals('{"dave":"is ace","tinder":"is amazing"}', $app->handle($request)->getContent());
    }

    /**
     * @test
     */
    public function shouldAllowServicesAsControllers()
    {
        $request = Request::create('/');
        $app = new Application();
        $app['my_controller'] = function() { return new MyController; };
        $app->get('/', 'my_controller:getAction');
        $this->assertEquals('Tinder\Test\MyController::getAction', $app->handle($request)->getContent());
    }

    /**
     * @test
     */
    public function shouldRenderTemplate()
    {

        $app = new Application();
        $app->register(new \Silex\Provider\TwigServiceProvider(), array(
            'twig.path' => __DIR__.'/views',
        ));

        $app->get('/', function() { return array('name' => 'Dave'); })
            ->template('hello_world.html.twig');

        $request = Request::create('/');
        $this->assertEquals("Hello Dave\n", $app->handle($request)->getContent());
    }

    /**
     * @test
     */
    public function shouldCallCallbackBeforeRenderingTemplate()
    {
        $app = new Application();
        $app->register(new \Silex\Provider\TwigServiceProvider(), array(
            'twig.path' => __DIR__.'/views',
        ));

        $app->get('/', function() {
            return array('name' => 'Dave');
        })
        ->template('hello_world.html.twig', function($data) {
            $data['name'] = 'Tinder'; return $data;
        });

        $request = Request::create('/');
        $this->assertEquals("Hello Tinder\n", $app->handle($request)->getContent());
    }

    /**
     * @test
     */
    public function shouldRedirectToStringUrl()
    {
        $url = "/dave";

        $app = new Application();
        $app->post("/blah", function() { return; })
            ->redirect($url);

        $request = Request::create('/blah', 'POST');
        $response = $app->handle($request);

        $this->assertInstanceOf("Symfony\Component\HttpFoundation\RedirectResponse", $response);
        $this->assertEquals($url, $response->getTargetUrl());
    }

    /**
     * @test
     */
    public function shouldRedirectToCallable()
    {
        $url = function() { return "/dave2"; };

        $app = new Application();
        $app->post("/blah", function() { return; })
            ->redirect($url);

        $request = Request::create('/blah', 'POST');
        $response = $app->handle($request);

        $this->assertInstanceOf("Symfony\Component\HttpFoundation\RedirectResponse", $response);
        $this->assertEquals("/dave2", $response->getTargetUrl());
    }

    /**
     * @test
     */
    public function shouldRedirectToCallableWithArguments()
    {
        $url = function($id) { return "/user/$id"; };

        $app = new Application();
        $app->post("/user/{id}", function() { return; })
            ->redirect($url);

        $request = Request::create('/user/12', 'POST');
        $response = $app->handle($request);

        $this->assertInstanceOf("Symfony\Component\HttpFoundation\RedirectResponse", $response);
        $this->assertEquals("/user/12", $response->getTargetUrl());
    }

    /**
     * @test
     */
    public function shouldRedirectUsingUrlGenerator()
    {
        $url = function($id) { return "/user/$id"; };

        $app = new Application();
        $app->register(new \Silex\Provider\UrlGeneratorServiceProvider());

        $app->post("/user/{id}", function() { return; })
            ->redirect("get_user", function($id) { return array("id" => $id); });

        $app->get("/user/{id}", function() { return; })
            ->bind("get_user");

        $request = Request::create('/user/12', 'POST');
        $response = $app->handle($request);

        $this->assertInstanceOf("Symfony\Component\HttpFoundation\RedirectResponse", $response);
        $this->assertEquals("/user/12", $response->getTargetUrl());
    }

    /**
     * @test
     */
    public function shouldNotRedirectWhenCustomErrorHandlerReturnsNull()
    {
        $app = new Application();

        $app->error(function() { });

        $app->post("/user/{id}", function() { return; })
            ->redirect("/")
            ->before(function() { throw new \Exception(); });

        $request = Request::create('/user/12', 'POST');
        $response = $app->handle($request);

        $this->assertNotInstanceOf("Symfony\Component\HttpFoundation\RedirectResponse", $response);
    }
}

class MyController
{
    public function getAction()
    {
        return __METHOD__;
    }
}
