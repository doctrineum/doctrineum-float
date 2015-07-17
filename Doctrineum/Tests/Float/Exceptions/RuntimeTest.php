<?php
namespace Doctrineum\Float\Exceptions;

class RuntimeTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function is_interface()
    {
        $this->assertTrue(interface_exists(Runtime::class));
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
