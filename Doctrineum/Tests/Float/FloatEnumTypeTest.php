<?php
namespace Doctrineum\Tests\Float;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Doctrineum\Float\FloatEnum;
use Doctrineum\Float\FloatEnumInterface;
use Doctrineum\Float\FloatEnumType;
use Doctrineum\Scalar\Enum;

class FloatEnumTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return \Doctrineum\Float\FloatEnumType
     */
    protected function getEnumTypeClass()
    {
        return FloatEnumType::getClass();
    }

    /**
     * @return \Doctrineum\Float\FloatEnum
     */
    protected function getRegisteredEnumClass()
    {
        return FloatEnum::getClass();
    }

    protected function tearDown()
    {
        \Mockery::close();

        $enumTypeClass = $this->getEnumTypeClass();
        $enumType = Type::getType($enumTypeClass::getTypeName());
        /** @var FloatEnumType $enumType */
        if ($enumType::hasSubTypeEnum($this->getSubTypeEnumClass())) {
            self::assertTrue($enumType::removeSubTypeEnum($this->getSubTypeEnumClass()));
        }
    }

    /**
     * @test
     */
    public function can_be_registered()
    {
        $enumTypeClass = $this->getEnumTypeClass();
        Type::addType($enumTypeClass::getTypeName(), $enumTypeClass);
        /** @var \PHPUnit_Framework_TestCase $this */
        self::assertTrue(Type::hasType($enumTypeClass::getTypeName()));
    }

    /**
     * @test
     * @depends can_be_registered
     */
    public function type_instance_can_be_obtained()
    {
        $enumTypeClass = $this->getEnumTypeClass();
        $instance = $enumTypeClass::getType($enumTypeClass::getTypeName());
        /** @var \PHPUnit_Framework_TestCase $this */
        self::assertInstanceOf($enumTypeClass, $instance);

        return $instance;
    }

    /**
     * @param FloatEnumType $enumType
     *
     * @test
     * @depends type_instance_can_be_obtained
     */
    public function type_name_is_as_expected(FloatEnumType $enumType)
    {
        $enumTypeClass = $this->getEnumTypeClass();
        // like self_typed_float_enum
        $typeName = $this->convertToTypeName($enumTypeClass);
        // like SELF_TYPED_FLOAT_ENUM
        $constantName = strtoupper($typeName);
        self::assertTrue(defined("$enumTypeClass::$constantName"));
        self::assertSame($enumTypeClass::getTypeName(), $typeName);
        self::assertSame($typeName, constant("$enumTypeClass::$constantName"));
        self::assertSame($enumType::getTypeName(), $enumTypeClass::getTypeName());
    }

    /**
     * @param string $className
     * @return string
     */
    private function convertToTypeName($className)
    {
        $withoutType = preg_replace('~Type$~', '', $className);
        $parts = explode('\\', $withoutType);
        $baseClassName = $parts[count($parts) - 1];
        preg_match_all('~(?<words>[A-Z][^A-Z]+)~', $baseClassName, $matches);
        $concatenated = implode('_', $matches['words']);

        return strtolower($concatenated);
    }

    /**
     * @param FloatEnumType $enumType
     *
     * @test
     * @depends type_instance_can_be_obtained
     */
    public function sql_declaration_is_valid(FloatEnumType $enumType)
    {
        $sql = $enumType->getSQLDeclaration([], $this->getAbstractPlatform());
        $defaultLength = $enumType->getDefaultLength($this->getAbstractPlatform());
        $decimalPrecision = $enumType->getDecimalPrecision($this->getAbstractPlatform());
        /** @var \PHPUnit_Framework_TestCase $this */
        self::assertSame("DECIMAL($defaultLength,$decimalPrecision)", $sql);
    }

    /**
     * @return AbstractPlatform
     */
    private function getAbstractPlatform()
    {
        return \Mockery::mock(AbstractPlatform::class);
    }

    /**
     * @param FloatEnumType $enumType
     *
     * @test
     * @depends type_instance_can_be_obtained
     */
    public function sql_default_length_is_sixty_five(FloatEnumType $enumType)
    {
        $defaultLength = $enumType->getDefaultLength($this->getAbstractPlatform());
        /** @var \PHPUnit_Framework_TestCase $this */
        self::assertSame(65, $defaultLength);
    }

    /**
     * @param FloatEnumType $enumType
     *
     * @test
     * @depends type_instance_can_be_obtained
     */
    public function sql_decimal_precision_is_thirty(FloatEnumType $enumType)
    {
        $defaultLength = $enumType->getDecimalPrecision($this->getAbstractPlatform());
        /** @var \PHPUnit_Framework_TestCase $this */
        self::assertSame(30, $defaultLength);
    }

    /**
     * @param FloatEnumType $enumType
     *
     * @test
     * @depends type_instance_can_be_obtained
     */
    public function enum_as_database_value_is_float_value_of_that_enum(FloatEnumType $enumType)
    {
        $enum = \Mockery::mock(Enum::class);
        /** @noinspection PhpMethodParametersCountMismatchInspection */
        $enum->shouldReceive('getValue')
            ->once()
            ->andReturn($value = 12345.67859);
        /** @var Enum $enum */
        self::assertSame($value, $enumType->convertToDatabaseValue($enum, $this->getAbstractPlatform()));
    }

    /**
     * CONVERSIONS TO PHP VALUE
     */

    /**
     * @param FloatEnumType $enumType
     *
     * @test
     * @depends type_instance_can_be_obtained
     */
    public function float_to_php_value_gives_enum_with_that_float(FloatEnumType $enumType)
    {
        $enum = $enumType->convertToPHPValue($float = 12345.67859, $this->getAbstractPlatform());
        self::assertInstanceOf($this->getRegisteredEnumClass(), $enum);
        self::assertSame($float, $enum->getValue());
        self::assertSame("$float", (string)$enum);
    }

    /**
     * @param FloatEnumType $enumType
     *
     * @test
     * @depends type_instance_can_be_obtained
     */
    public function string_float_to_php_value_gives_enum_with_that_float(FloatEnumType $enumType)
    {
        $value = $enumType->convertToPHPValue($stringFloat = '12345.67859', $this->getAbstractPlatform());
        self::assertInstanceOf($this->getRegisteredEnumClass(), $value);
        self::assertSame((float)$stringFloat, $value->getValue());
    }

    /**
     * @param FloatEnumType $enumType
     *
     * @test
     * @depends type_instance_can_be_obtained
     */
    public function null_to_php_value_is_zero(FloatEnumType $enumType)
    {
        $enum = $enumType->convertToPHPValue($null = null, $this->getAbstractPlatform());
        self::assertSame(0.0, $enum->getValue());
        self::assertSame((float)$null, $enum->getValue());
        self::assertSame((string)(float)$null, (string)$enum);
    }

    /**
     * @param FloatEnumType $enumType
     *
     * @test
     * @depends type_instance_can_be_obtained
     */
    public function empty_string_to_php_is_zero(FloatEnumType $enumType)
    {
        $enum = $enumType->convertToPHPValue($emptyString = '', $this->getAbstractPlatform());
        self::assertSame(0.0, $enum->getValue());
        self::assertSame((float)$emptyString, $enum->getValue());
        self::assertSame((string)(float)$emptyString, (string)$enum);
    }

    /**
     * @param FloatEnumType $enumType
     *
     * @test
     * @depends type_instance_can_be_obtained
     */
    public function integer_to_php_value_gives_enum_with_float(FloatEnumType $enumType)
    {
        $enum = $enumType->convertToPHPValue($integer = 12345, $this->getAbstractPlatform());
        self::assertInstanceOf($this->getRegisteredEnumClass(), $enum);
        self::assertSame(12345.0, $enum->getValue());
        self::assertSame((float)$integer, $enum->getValue());
        self::assertSame((string)(float)$integer, (string)$enum);
    }

    /**
     * @param FloatEnumType $enumType
     *
     * @test
     * @depends type_instance_can_be_obtained
     */
    public function zero_integer_to_php_value_gives_enum_with_float(FloatEnumType $enumType)
    {
        $enum = $enumType->convertToPHPValue($zeroInteger = 0, $this->getAbstractPlatform());
        self::assertInstanceOf($this->getRegisteredEnumClass(), $enum);
        self::assertSame(0.0, $enum->getValue());
        self::assertSame((float)$zeroInteger, $enum->getValue());
        self::assertSame((string)(float)$zeroInteger, (string)$enum);
    }

    /**
     * @param FloatEnumType $enumType
     *
     * @test
     * @depends type_instance_can_be_obtained
     */
    public function false_to_php_value_is_zero(FloatEnumType $enumType)
    {
        $enum = $enumType->convertToPHPValue($false = false, $this->getAbstractPlatform());
        self::assertSame(0.0, $enum->getValue());
        self::assertSame((float)$false, $enum->getValue());
        self::assertSame((string)(float)$false, (string)$enum);
    }

    /**
     * @param FloatEnumType $enumType
     *
     * @test
     * @depends type_instance_can_be_obtained
     */
    public function true_to_php_value_is_one(FloatEnumType $enumType)
    {
        $enum = $enumType->convertToPHPValue($true = true, $this->getAbstractPlatform());
        self::assertSame(1.0, $enum->getValue());
        self::assertSame((float)$true, $enum->getValue());
        self::assertSame((string)(float)$true, (string)$enum);
    }

    /**
     * @param FloatEnumType $enumType
     *
     * @test
     * @depends type_instance_can_be_obtained
     * @expectedException \Doctrineum\Float\Exceptions\UnexpectedValueToConvert
     */
    public function array_to_php_value_cause_exception(FloatEnumType $enumType)
    {
        $enumType->convertToPHPValue([], $this->getAbstractPlatform());
    }

    /**
     * @param FloatEnumType $enumType
     *
     * @test
     * @depends type_instance_can_be_obtained
     * @expectedException \Doctrineum\Float\Exceptions\UnexpectedValueToConvert
     */
    public function resource_to_php_value_cause_exception(FloatEnumType $enumType)
    {
        $enumType->convertToPHPValue(tmpfile(), $this->getAbstractPlatform());
    }

    /**
     * @param FloatEnumType $enumType
     *
     * @test
     * @depends type_instance_can_be_obtained
     * @expectedException \Doctrineum\Float\Exceptions\UnexpectedValueToConvert
     */
    public function object_to_php_value_cause_exception(FloatEnumType $enumType)
    {
        $enumType->convertToPHPValue(new \stdClass(), $this->getAbstractPlatform());
    }

    /**
     * @param FloatEnumType $enumType
     *
     * @test
     * @depends type_instance_can_be_obtained
     * @expectedException \Doctrineum\Float\Exceptions\UnexpectedValueToConvert
     */
    public function callback_to_php_value_cause_exception(FloatEnumType $enumType)
    {
        $enumType->convertToPHPValue(
            function () {
            },
            $this->getAbstractPlatform()
        );
    }

    /**
     * subtype tests
     */

    /**
     * @param FloatEnumType $enumType
     * @return FloatEnumType
     *
     * @test
     * @depends type_instance_can_be_obtained
     */
    public function can_register_subtype(FloatEnumType $enumType)
    {
        self::assertTrue($enumType::addSubTypeEnum($this->getSubTypeEnumClass(), '~foo~'));
        self::assertTrue($enumType::hasSubTypeEnum($this->getSubTypeEnumClass()));

        return $enumType;
    }

    /**
     * @param FloatEnumType $enumType
     *
     * @test
     * @depends can_register_subtype
     */
    public function can_unregister_subtype(FloatEnumType $enumType)
    {
        /**
         * The subtype is unregistered because of tearDown clean up
         * @see FloatEnumTypeTestTrait::tearDown
         */
        self::assertFalse($enumType::hasSubTypeEnum($this->getSubTypeEnumClass()), 'Subtype should not be registered yet');
        self::assertTrue($enumType::addSubTypeEnum($this->getSubTypeEnumClass(), '~foo~'));
        self::assertTrue($enumType::removeSubTypeEnum($this->getSubTypeEnumClass()));
        self::assertFalse($enumType::hasSubTypeEnum($this->getSubTypeEnumClass()));
    }

    /**
     * @param FloatEnumType $enumType
     *
     * @test
     * @depends can_register_subtype
     */
    public function subtype_returns_proper_enum(FloatEnumType $enumType)
    {
        $enumType::addSubTypeEnum($this->getSubTypeEnumClass(), $regexp = '~45.6~');
        self::assertTrue($enumType::hasSubTypeEnum($this->getSubTypeEnumClass()));
        /** @var AbstractPlatform $abstractPlatform */
        $abstractPlatform = \Mockery::mock(AbstractPlatform::class);
        $matchingValueToConvert = 12345.6789;
        self::assertRegExp($regexp, "$matchingValueToConvert");
        /**
         * Used TestSubtype returns as an "enum" the given value, which is $valueToConvert in this case,
         * @see \Doctrineum\Tests\Scalar\TestSubtype::getEnum
         */
        $enumFromSubType = $enumType->convertToPHPValue($matchingValueToConvert, $abstractPlatform);
        self::assertInstanceOf($this->getSubTypeEnumClass(), $enumFromSubType);
        self::assertSame("$matchingValueToConvert", "$enumFromSubType");
    }

    /**
     * @param FloatEnumType $enumType
     *
     * @test
     * @depends can_register_subtype
     */
    public function default_enum_is_given_if_subtype_does_not_match(FloatEnumType $enumType)
    {
        $enumType::addSubTypeEnum($this->getSubTypeEnumClass(), $regexp = '~45.6~');
        self::assertTrue($enumType::hasSubTypeEnum($this->getSubTypeEnumClass()));
        /** @var AbstractPlatform $abstractPlatform */
        $abstractPlatform = \Mockery::mock(AbstractPlatform::class);
        $nonMatchingValueToConvert = 9999.9999;
        self::assertNotRegExp($regexp, "$nonMatchingValueToConvert");
        /**
         * Used TestSubtype returns as an "enum" the given value, which is $valueToConvert in this case,
         * @see \Doctrineum\Tests\Scalar\TestSubtype::getEnum
         */
        $enum = $enumType->convertToPHPValue($nonMatchingValueToConvert, $abstractPlatform);
        self::assertNotSame($nonMatchingValueToConvert, $enum);
        self::assertInstanceOf(FloatEnumInterface::class, $enum);
        self::assertSame("$nonMatchingValueToConvert", (string)$enum);
    }

    /**
     * @param FloatEnumType $enumType
     *
     * @test
     * @depends type_instance_can_be_obtained
     * @expectedException \Doctrineum\Scalar\Exceptions\SubTypeEnumIsAlreadyRegistered
     */
    public function registering_same_subtype_again_throws_exception(FloatEnumType $enumType)
    {
        self::assertFalse($enumType::hasSubTypeEnum($this->getSubTypeEnumClass()));
        self::assertTrue($enumType::addSubTypeEnum($this->getSubTypeEnumClass(), '~foo~'));
        // registering twice - should thrown an exception
        $enumType::addSubTypeEnum($this->getSubTypeEnumClass(), '~foo~');
    }

    /**
     * @param FloatEnumType $enumType
     *
     * @test
     * @depends type_instance_can_be_obtained
     * @expectedException \Doctrineum\Scalar\Exceptions\SubTypeEnumClassNotFound
     */
    public function registering_non_existing_subtype_class_throws_exception(FloatEnumType $enumType)
    {
        $enumType::addSubTypeEnum('NonExistingClassName', '~foo~');
    }

    /**
     * @param FloatEnumType $enumType
     *
     * @test
     * @depends type_instance_can_be_obtained
     * @expectedException \Doctrineum\Scalar\Exceptions\SubTypeEnumHasToBeEnum
     */
    public function registering_subtype_class_without_proper_method_throws_exception(FloatEnumType $enumType)
    {
        $enumType::addSubTypeEnum(\stdClass::class, '~foo~');
    }

    /**
     * @param FloatEnumType $enumType
     *
     * @test
     * @depends type_instance_can_be_obtained
     * @expectedException \Doctrineum\Scalar\Exceptions\InvalidRegexpFormat
     * @expectedExceptionMessage The given regexp is not enclosed by same delimiters and therefore is not valid: 'foo~'
     */
    public function registering_subtype_with_invalid_regexp_throws_exception(FloatEnumType $enumType)
    {
        $enumType::addSubTypeEnum($this->getSubTypeEnumClass(), 'foo~' /* missing opening delimiter */);
    }

    /**
     * @test
     */
    public function can_register_another_enum_type()
    {
        $anotherEnumType = $this->getAnotherEnumTypeClass();
        if (!$anotherEnumType::isRegistered()) {
            self::assertTrue($anotherEnumType::registerSelf());
        } else {
            self::assertFalse($anotherEnumType::registerSelf());
        }

        self::assertTrue($anotherEnumType::isRegistered());
    }

    /**
     * @test
     *
     * @depends can_register_another_enum_type
     */
    public function different_types_with_same_subtype_regexp_distinguish_them()
    {
        $enumTypeClass = $this->getEnumTypeClass();
        if ($enumTypeClass::hasSubTypeEnum($this->getSubTypeEnumClass())) {
            $enumTypeClass::removeSubTypeEnum($this->getSubTypeEnumClass());
        }
        $enumTypeClass::addSubTypeEnum($this->getSubTypeEnumClass(), $regexp = '~[4-6]+~');

        $anotherEnumTypeClass = $this->getAnotherEnumTypeClass();
        if ($anotherEnumTypeClass::hasSubTypeEnum($this->getAnotherSubTypeEnumClass())) {
            $anotherEnumTypeClass::removeSubTypeEnum($this->getAnotherSubTypeEnumClass());
        }
        // regexp is same, sub-type is not
        $anotherEnumTypeClass::addSubTypeEnum($this->getAnotherSubTypeEnumClass(), $regexp);

        $value = 345.678;
        self::assertRegExp($regexp, "$value");

        $enumType = Type::getType($enumTypeClass::getTypeName());
        $enumSubType = $enumType->convertToPHPValue($value, $this->getPlatform());
        self::assertInstanceOf($this->getSubTypeEnumClass(), $enumSubType);
        self::assertSame("$value", "$enumSubType");

        $anotherEnumType = Type::getType($anotherEnumTypeClass::getTypeName());
        $anotherEnumSubType = $anotherEnumType->convertToPHPValue($value, $this->getPlatform());
        self::assertInstanceOf($this->getSubTypeEnumClass(), $enumSubType);
        self::assertSame("$value", "$anotherEnumSubType");

        // registered sub-types are different, although regexp is the same - let's test if they are kept separately
        self::assertNotSame($enumSubType, $anotherEnumSubType);
    }

    /**
     * @return AbstractPlatform
     */
    protected function getPlatform()
    {
        return \Mockery::mock(AbstractPlatform::class);
    }

    /**
     * @return string
     */
    protected function getSubTypeEnumClass()
    {
        return TestSubTypeFloatEnum::class;
    }

    /**
     * @return string
     */
    protected function getAnotherSubTypeEnumClass()
    {
        return TestAnotherSubTypeFloatEnum::class;
    }

    /**
     * @return FloatEnumType|string
     */
    protected function getAnotherEnumTypeClass()
    {
        return TestAnotherFloatEnumType::class;
    }

}

/** inner */
class TestSubTypeFloatEnum extends FloatEnum
{

}

class TestAnotherSubTypeFloatEnum extends FloatEnum
{

}

class TestAnotherFloatEnumType extends FloatEnumType
{

}
