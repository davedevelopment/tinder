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
}


class MyController
{
    public function getAction() { 
        return __METHOD__;
    }
}
