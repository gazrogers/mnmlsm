<?php
namespace Test\Functional\BusinessLogic;

use Error;
use Exception;
use Phalcon\Mvc\Micro;
use Library\Exceptions\NotFound;
use Model\BusinessLogic\ErrorHandler;

// Declare a dummy class for testing purposes
class JimBob {}

class ErrorHandlerTest extends \Codeception\Test\Unit
{
    /**
     * @var \FunctionalTester
     */
    protected $tester;
    protected $app;
    protected $dummyLog;
    
    protected function _before()
    {
        $this->tester = new ErrorHandler();
        // Set up a test version of the app with a dummy logging class
        $this->dummyLog = new class{public $log; public function error($message){$this->log .= $message;}};
        $this->app = new Micro();
        $this->app->di->set('logger', $this->dummyLog);
    }

    protected function _after()
    {
    }

    public function testCustomExceptionHandling()
    {
        $this->tester->handle(new NotFound("Test exception"), $this->app);

        $this->assertEquals(404, $this->app->response->getStatusCode());
        $this->assertEquals("Not Found", $this->app->response->getReasonPhrase());
        $this->assertEquals('{"errors":[{"message":"Test exception"}]}', $this->app->response->getContent());
    }

    public function testGenericExceptionHandling()
    {
        $this->tester->handle(new Exception("Ooh it's all gone wrong"), $this->app);

        $this->assertEquals(500, $this->app->response->getStatusCode());
        $this->assertEquals("Internal Server Error", $this->app->response->getReasonPhrase());
        $this->assertEquals('{"errors":[{"message":"Ooh it\'s all gone wrong"}]}', $this->app->response->getContent());
    }

    public function testErrorHandling()
    {
        $this->tester->handle(new Error("Danger Will Robinson"), $this->app);

        $this->assertEquals(500, $this->app->response->getStatusCode());
        $this->assertEquals("Internal Server Error", $this->app->response->getReasonPhrase());
        $this->assertEquals('{"errors":[{"message":"Server error"}]}', $this->app->response->getContent());
        $this->assertEquals('Danger Will Robinson', $this->dummyLog->log);
    }

    public function testUnexpectedClassHandling()
    {
        $this->tester->handle(new JimBob(), $this->app);

        $this->assertEquals(500, $this->app->response->getStatusCode());
        $this->assertEquals("Internal Server Error", $this->app->response->getReasonPhrase());
        $this->assertEquals('{"errors":[{"message":"Server error"}]}', $this->app->response->getContent());
        $this->assertEquals('Unhandled error class: Test\Functional\BusinessLogic\JimBob', $this->dummyLog->log);
    }
}