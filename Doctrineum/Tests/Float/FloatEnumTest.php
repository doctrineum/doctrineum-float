<?php
namespace Doctrineum\Tests\Float;

use Doctrineum\Float\FloatEnum;
use Doctrineum\Scalar\Enum;
use Doctrineum\Tests\Scalar\WithToStringTestObject;

class FloatEnumTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return \Doctrineum\Float\FloatEnum
     */
    protected function getEnumClass()
    {
        return FloatEnum::getClass();
    }

    /** @test */
    public function can_create_enum_instance()
    {
        $enumClass = $this->getEnumClass();
        $instance = $enumClass::getEnum(12345.6789);

        self::assertInstanceOf($enumClass, $instance);
    }

    /** @test */
    public function returns_the_same_float_as_created_with()
    {
        $enumClass = $this->getEnumClass();
        $enum = $enumClass::getEnum($float = 12345.6789);

        self::assertSame($float, $enum->getValue());
        self::assertSame("$float", (string)$enum);
    }

    /** @test */
    public function returns_float_created_from_string_created_with()
    {
        $enumClass = $this->getEnumClass();
        $enum = $enumClass::getEnum($stringFloat = '12345.6789');

        self::assertSame((float)$stringFloat, $enum->getValue());
        self::assertSame($stringFloat, (string)$enum);
    }

    /** @test */
    public function string_with_float_and_spaces_is_trimmed_and_accepted()
    {
        $enumClass = $this->getEnumClass();
        $enum = $enumClass::getEnum('  12.34 ');

        self::assertSame(12.34, $enum->getValue());
        self::assertSame('12.34', (string)$enum);
    }

    /**
     * @test
     */
    public function integer_is_allowed()
    {
        $enumClass = $this->getEnumClass();
        $enum = $enumClass::getEnum(123);

        self::assertSame(123.0, $enum->getValue());
    }

    /**
     * @test
     */
    public function string_integer_is_allowed()
    {
        $enumClass = $this->getEnumClass();
        $enum = $enumClass::getEnum('123');

        self::assertSame(123.0, $enum->getValue());
    }

    /**
     * @test
     */
    public function string_starting_by_float_is_that_float()
    {
        $enumClass = $this->getEnumClass();
        $enum = $enumClass::getEnum('12.34foo');

        self::assertSame(12.34, $enum->getValue());
    }

    /**
     * @test
     */
    public function object_with_float_and_to_string_can_be_used()
    {
        $enumClass = $this->getEnumClass();
        $enum = $enumClass::getEnum(new WithToStringTestObject($float = 12345.6789));

        self::assertInstanceOf(Enum::class, $enum);
        self::assertSame($float, $enum->getValue());
        self::assertSame("$float", (string)$enum);
    }

    /**
     * @test
     */
    public function object_with_integer_and_to_string_can_be_used()
    {
        $enumClass = $this->getEnumClass();
        $enum = $enumClass::getEnum(new WithToStringTestObject($integer = 12345));

        self::assertInstanceOf(Enum::class, $enum);
        self::assertSame((float)$integer, $enum->getValue());
        self::assertSame("$integer", (string)$enum);
    }

    /**
     * @test
     */
    public function object_with_characters_only_is_zero()
    {
        $enumClass = $this->getEnumClass();
        $enum = $enumClass::getEnum(new WithToStringTestObject('foo'));

        self::assertSame(0.0, $enum->getValue());
    }

    /**
     * @test
     */
    public function empty_string_is_zero()
    {
        $enumClass = $this->getEnumClass();
        $enum = $enumClass::getEnum('');

        self::assertSame(0.0, $enum->getValue());
    }

    /**
     * @test
     */
    public function characters_only_is_zero()
    {
        $enumClass = $this->getEnumClass();
        $enum = $enumClass::getEnum('foo');

        self::assertSame(0.0, $enum->getValue());
    }

    /**
     * @test
     */
    public function null_is_zero()
    {
        $enumClass = $this->getEnumClass();
        $enum = $enumClass::getEnum(null);

        self::assertSame(0.0, $enum->getValue());
    }

    /**
     * @test
     */
    public function inherited_enum_with_same_value_lives_in_own_inner_namespace()
    {
        $enumClass = $this->getEnumClass();

        $enum = $enumClass::getEnum($value = 12345.6789);
        self::assertInstanceOf($enumClass, $enum);
        self::assertSame($value, $enum->getValue());
        self::assertSame("$value", (string)$enum);

        $inDifferentNamespace = $this->getInheritedEnum($value);
        self::assertInstanceOf($enumClass, $inDifferentNamespace);
        self::assertSame($enum->getValue(), $inDifferentNamespace->getValue());
        self::assertNotSame($enum, $inDifferentNamespace);
    }

    protected function getInheritedEnum($value)
    {
        return new TestInheritedFloatEnum($value);
    }
}

/** inner */
class TestInheritedFloatEnum extends FloatEnum
{

}
