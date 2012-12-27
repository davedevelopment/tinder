<?php

namespace Tinder\Test\Controller;

use Silex\Tests\ControllerResolverTest as BaseControllerResolverTest;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Tinder\Controller\ControllerResolver;

class ControllerResolverTest extends BaseControllerResolverTest
{
    /**
     * @test
     * @expectedException LogicException 
     */
    public function shouldThrowIfUnknownConvention()
    {
        $res = new ControllerResolver(new Application);
        $req = Request::create("/dave");
        $req->attributes->set('_controller', 'some_incorrect_convention');
        $res->getController($req);
    } 

    /**
     * @test
     * @expectedException InvalidArgumentException 
     * @expectedExceptionMessage Service "some_missing_controller" does not exist.
     */
    public function shouldThrowIfAServiceIsNotDefined()
    {
        $res = new ControllerResolver(new Application);
        $req = Request::create("/dave");
        $req->attributes->set('_controller', 'some_missing_controller:dave');
        $res->getController($req);
    } 

    /**
     * @test
     */
    public function shouldPrepareParamsForInject()
    {
        $res = new ControllerResolver(new Application);
        $req = Request::create("/dave", "POST", $expected = array('dave' => 'is ace'));
        $args = $res->getArguments($req, function(array $params) {});
        $this->assertEquals(array($expected), $args);
    }

    /**
     * @test
     */
    public function shouldPrepareQueryForInjection()
    {
        $res = new ControllerResolver(new Application);
        $req = Request::create("/dave", "GET", $expected = array('dave' => 'is ace'));
        $args = $res->getArguments($req, function(array $query) {});
        $this->assertEquals(array($expected), $args);
    }
}
