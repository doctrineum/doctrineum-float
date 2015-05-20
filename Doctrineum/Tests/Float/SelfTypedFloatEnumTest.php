<?php
namespace Doctrineum\Float;

use Doctrine\DBAL\Types\Type;
use Doctrineum\Tests\Float\FloatEnumTestTrait;
use Doctrineum\Tests\Float\FloatEnumTypeTestTrait;

class SelfTypedFloatEnumTest extends \PHPUnit_Framework_TestCase
{

    use FloatEnumTestTrait;
    use FloatEnumTypeTestTrait;

    /**
     * Overloaded parent test to test self-registration
     *
     * @test
     */
    public function can_be_registered()
    {
        $enumTypeClass = $this->getEnumTypeClass();
        $enumTypeClass::registerSelf();
        $this->assertTrue(Type::hasType($enumTypeClass::getTypeName()));
    }

    /**
     * @test
     * @depends can_be_registered
     */
    public function repeated_self_registration_returns_false()
    {
        $this->assertFalse(SelfTypedFloatEnum::registerSelf());
    }

    protected function getInheritedEnum($value)
    {
        if (!Type::hasType(TestInheritedSelfTypedFloatEnum::getTypeName())) {
            TestInheritedSelfTypedFloatEnum::registerSelf();
        }
        $enum = TestInheritedSelfTypedFloatEnum::getEnum($value);

        return $enum;
    }

    /**
     * @return string
     */
    protected function getSubTypeEnumClass()
    {
        return TestSubTypeSelfTypedFloatEnum::class;
    }

    /**
     * @return string
     */
    protected function getAnotherSubTypeEnumClass()
    {
        return TestAnotherSubTypeSelfTypedFloatEnum::class;
    }

    /**
     * @return string
     */
    protected function getAnotherEnumTypeClass()
    {
        return TestAnotherSelfTypedFloatEnum::class;
    }

}

/** inner */
class TestInheritedSelfTypedFloatEnum extends SelfTypedFloatEnum
{

}

class TestSubTypeSelfTypedFloatEnum extends SelfTypedFloatEnum
{

}

class TestAnotherSubTypeSelfTypedFloatEnum extends SelfTypedFloatEnum
{

}

class TestAnotherSelfTypedFloatEnum extends SelfTypedFloatEnum
{

}
