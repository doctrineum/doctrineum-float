<?php
namespace Doctrineum\Tests\Float;

use Doctrineum\Float\FloatEnum;
use Doctrineum\Float\SelfTypedFloatEnum;
use Doctrineum\Scalar\EnumInterface;
use Doctrineum\Tests\Scalar\WithToStringTestObject;

trait FloatEnumTestTrait
{
    /**
     * @return \Doctrineum\Float\FloatEnum|\Doctrineum\Float\SelfTypedFloatEnum
     */
    protected function getEnumClass()
    {
        return preg_replace('~Test$~', '', static::class);
    }

    /** @test */
    public function can_create_enum_instance()
    {
        $enumClass = $this->getEnumClass();
        $instance = $enumClass::getEnum(12345.6789);
        /** @var \PHPUnit_Framework_TestCase $this */
        $this->assertInstanceOf($enumClass, $instance);
    }

    /** @test */
    public function returns_the_same_float_as_created_with()
    {
        $enumClass = $this->getEnumClass();
        $enum = $enumClass::getEnum($float = 12345.6789);
        /** @var \PHPUnit_Framework_TestCase $this */
        $this->assertSame($float, $enum->getEnumValue());
        $this->assertSame("$float", (string)$enum);
    }

    /** @test */
    public function returns_float_created_from_string_created_with()
    {
        $enumClass = $this->getEnumClass();
        $enum = $enumClass::getEnum($stringFloat = '12345.6789');
        /** @var \PHPUnit_Framework_TestCase $this */
        $this->assertSame(floatval($stringFloat), $enum->getEnumValue());
        $this->assertSame($stringFloat, (string)$enum);
    }

    /** @test */
    public function string_with_float_and_spaces_is_trimmed_and_accepted()
    {
        $enumClass = $this->getEnumClass();
        $enum = $enumClass::getEnum('  12.34 ');
        /** @var \PHPUnit_Framework_TestCase $this */
        $this->assertSame(12.34, $enum->getEnumValue());
        $this->assertSame('12.34', (string)$enum);
    }

    /**
     * @test
     */
    public function integer_is_allowed()
    {
        $enumClass = $this->getEnumClass();
        $enum = $enumClass::getEnum(123);
        /** @var \PHPUnit_Framework_TestCase $this */
        $this->assertSame(123.0, $enum->getEnumValue());
    }

    /**
     * @test
     */
    public function string_integer_is_allowed()
    {
        $enumClass = $this->getEnumClass();
        $enum = $enumClass::getEnum('123');
        /** @var \PHPUnit_Framework_TestCase $this */
        $this->assertSame(123.0, $enum->getEnumValue());
    }

    /**
     * @test
     */
    public function string_starting_by_float_is_that_float()
    {
        $enumClass = $this->getEnumClass();
        $enum = $enumClass::getEnum('12.34foo');
        /** @var \PHPUnit_Framework_TestCase $this */
        $this->assertSame(12.34, $enum->getEnumValue());
    }

    /**
     * @test
     */
    public function object_with_float_and_to_string_can_be_used()
    {
        $enumClass = $this->getEnumClass();
        $enum = $enumClass::getEnum(new WithToStringTestObject($float = 12345.6789));
        /** @var \PHPUnit_Framework_TestCase $this */
        $this->assertInstanceOf(EnumInterface::class, $enum);
        $this->assertSame($float, $enum->getEnumValue());
        $this->assertSame("$float", (string)$enum);
    }

    /**
     * @test
     */
    public function object_with_integer_and_to_string_can_be_used()
    {
        $enumClass = $this->getEnumClass();
        $enum = $enumClass::getEnum(new WithToStringTestObject($integer = 12345));
        /** @var \PHPUnit_Framework_TestCase $this */
        $this->assertInstanceOf(EnumInterface::class, $enum);
        $this->assertSame(floatval($integer), $enum->getEnumValue());
        $this->assertSame("$integer", (string)$enum);
    }

    /**
     * @test
     */
    public function object_with_characters_only_is_zero()
    {
        $enumClass = $this->getEnumClass();
        $enum = $enumClass::getEnum(new WithToStringTestObject('foo'));
        /** @var \PHPUnit_Framework_TestCase|FloatEnumTestTrait $this */
        $this->assertSame(0.0, $enum->getEnumValue());
    }

    /**
     * @test
     */
    public function empty_string_is_zero()
    {
        $enumClass = $this->getEnumClass();
        $enum = $enumClass::getEnum('');
        /** @var \PHPUnit_Framework_TestCase|FloatEnumTestTrait $this */
        $this->assertSame(0.0, $enum->getEnumValue());
    }

    /**
     * @test
     */
    public function characters_only_is_zero()
    {
        $enumClass = $this->getEnumClass();
        $enum = $enumClass::getEnum('foo');
        /** @var \PHPUnit_Framework_TestCase|FloatEnumTestTrait $this */
        $this->assertSame(0.0, $enum->getEnumValue());
    }

    /**
     * @test
     */
    public function null_is_zero()
    {
        $enumClass = $this->getEnumClass();
        $enum = $enumClass::getEnum(null);
        /** @var \PHPUnit_Framework_TestCase|FloatEnumTestTrait $this */
        $this->assertSame(0.0, $enum->getEnumValue());
    }

    /**
     * @test
     */
    public function inherited_enum_with_same_value_lives_in_own_inner_namespace()
    {
        $enumClass = $this->getEnumClass();

        $enum = $enumClass::getEnum($value = 12345.6789);
        /** @var \PHPUnit_Framework_TestCase|FloatEnumTestTrait $this */
        $this->assertInstanceOf($enumClass, $enum);
        $this->assertSame($value, $enum->getEnumValue());
        $this->assertSame("$value", (string)$enum);

        $inDifferentNamespace = $this->getInheritedEnum($value);
        $this->assertInstanceOf($enumClass, $inDifferentNamespace);
        $this->assertSame($enum->getEnumValue(), $inDifferentNamespace->getEnumValue());
        $this->assertNotSame($enum, $inDifferentNamespace);
    }

    /**
     * @param $value
     * @return FloatEnum|SelfTypedFloatEnum
     */
    abstract protected function getInheritedEnum($value);
}
