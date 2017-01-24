<?php

namespace ZFTests\Controller;

use ZFTests\TestCases\ZFTestCase;
use ZFTests\Classes\TestController;

/**
 * Class ControllerTest
 * Controller tests
 *
 * @package ZFTests\Controller
 */
class ControllerTest extends ZFTestCase
{
    /**
     * Test controller init
     *
     * @covers          Controller::execute()
     * @dataProvider    actionMap
     */
    public function testExecute($action, $response){
        $ctrl = new TestController(self::$container);

        // Check expected response
        $this->assertEquals($response, $ctrl->execute($action));

        // Check if expected events were triggered
        $this->assertEventTriggered('controller:beforeexecute', function(){});
        $this->assertEventTriggered('controller:afterexecute', function(){});
    }

    /**
     * Test redirect flow
     *
     * @covers      Controller::setRedirect()
     * @covers      Controller::hasRedirect()
     */
    public function testRedirects(){
        $ctrl = new TestController(self::$container);

        $ctrl->setRedirect('/');

        $this->assertTrue($ctrl->hasRedirect());
    }

    /**
     * Actions and expected responses mapping set
     */
    public function actionMap(){
        return [
            ['ping', 'test'],
            ['_default', 'default']
        ];
    }
}
