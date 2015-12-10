<?php
namespace Doctrineum\Tests\Float\Exceptions;

use Doctrineum\Float\Exceptions\Exception;

class ExceptionTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function is_interface()
    {
        $this->assertTrue(interface_exists(Exception::class));
    }

    /**
     * @test
     * @expectedException \Doctrineum\Scalar\Exceptions\Exception
     */
    public function extends_base_doctrineum_interface()
    {
        throw new TestExceptionInterface();
    }

}

/** inner */
class TestExceptionInterface extends \Exception implements Exception
{

}
