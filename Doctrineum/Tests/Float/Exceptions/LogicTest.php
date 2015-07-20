<?php
namespace Doctrineum\Tests\Float\Exceptions;

use Doctrineum\Float\Exceptions\Logic;

class LogicTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function is_interface()
    {
        $this->assertTrue(interface_exists('Doctrineum\Float\Exceptions\Logic'));
    }

    /**
     * @test
     * @expectedException \Doctrineum\Scalar\Exceptions\Logic
     */
    public function extends_doctrineum_logic_interface()
    {
        throw new TestLogicInterface();
    }

    /**
     * @test
     * @expectedException \Doctrineum\Float\Exceptions\Exception
     */
    public function extends_local_mark_interface()
    {
        throw new TestLogicInterface();
    }
}

/** inner */
class TestLogicInterface extends \Exception implements Logic
{

}
