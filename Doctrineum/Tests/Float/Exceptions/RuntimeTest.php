<?php
namespace Doctrineum\Tests\Float\Exceptions;

use Doctrineum\Float\Exceptions\Runtime;

class RuntimeTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function is_interface()
    {
        $this->assertTrue(interface_exists('Doctrineum\Float\Exceptions\Runtime'));
    }

    /**
     * @test
     * @expectedException \Doctrineum\Scalar\Exceptions\Runtime
     */
    public function extends_doctrineum_logic_interface()
    {
        throw new TestRuntimeInterface();
    }

    /**
     * @test
     * @expectedException \Doctrineum\Float\Exceptions\Exception
     */
    public function extends_local_mark_interface()
    {
        throw new TestRuntimeInterface();
    }
}

/** inner */
class TestRuntimeInterface extends \Exception implements Runtime
{

}
