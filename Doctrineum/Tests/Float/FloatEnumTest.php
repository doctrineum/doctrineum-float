<?php
namespace Doctrineum\Float;

use Doctrineum\Tests\Float\FloatEnumTestTrait;

class FloatEnumTest extends \PHPUnit_Framework_TestCase
{
    use FloatEnumTestTrait;

    protected function getInheritedEnum($value)
    {
        return new TestInheritedFloatEnum($value);
    }
}

/** inner */
class TestInheritedFloatEnum extends FloatEnum
{

}
