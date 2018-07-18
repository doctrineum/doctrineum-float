<?php
declare(strict_types=1); // on PHP 7+ are standard PHP methods strict to types of given parameters

namespace Doctrineum\Tests\Float;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Doctrineum\Float\FloatEnum;
use Doctrineum\Float\FloatEnumInterface;
use Doctrineum\Float\FloatEnumType;
use Doctrineum\Scalar\ScalarEnumInterface;
use Doctrineum\Tests\SelfRegisteringType\AbstractSelfRegisteringTypeTest;

class FloatEnumTypeTest extends AbstractSelfRegisteringTypeTest
{
    protected function tearDown(): void
    {
        $enumType = Type::getType($this->getExpectedTypeName());
        /** @var FloatEnumType $enumType */
        if ($enumType::hasSubTypeEnum(TestSubTypeFloatEnum::class)) {
            self::assertTrue($enumType::removeSubTypeEnum(TestSubTypeFloatEnum::class));
        }
        if ($enumType::hasSubTypeEnum(TestAnotherSubTypeFloatEnum::class)) {
            self::assertTrue($enumType::removeSubTypeEnum(TestAnotherSubTypeFloatEnum::class));
        }

        parent::tearDown();
    }

    /**
     * @test
     * @return FloatEnumType
     * @throws \Doctrine\DBAL\DBALException
     */
    public function I_can_get_instance(): FloatEnumType
    {
        return parent::I_can_get_instance();
    }

    /**
     * @param FloatEnumType $enumType
     * @test
     * @depends I_can_get_instance
     */
    public function sql_declaration_is_valid(FloatEnumType $enumType): void
    {
        $sql = $enumType->getSQLDeclaration([], $this->getAbstractPlatform());
        $defaultLength = $enumType->getDefaultLength($this->getAbstractPlatform());
        $decimalPrecision = $enumType->getDecimalPrecision($this->getAbstractPlatform());
        self::assertSame("DECIMAL($defaultLength,$decimalPrecision)", $sql);
    }

    /**
     * @return AbstractPlatform|\Mockery\MockInterface
     */
    private function getAbstractPlatform(): AbstractPlatform
    {
        return $this->mockery(AbstractPlatform::class);
    }

    /**
     * @param FloatEnumType $enumType
     * @test
     * @depends I_can_get_instance
     */
    public function sql_default_length_is_sixty_five(FloatEnumType $enumType): void
    {
        $defaultLength = $enumType->getDefaultLength($this->getAbstractPlatform());
        self::assertSame(65, $defaultLength);
    }

    /**
     * @param FloatEnumType $enumType
     * @test
     * @depends I_can_get_instance
     */
    public function sql_decimal_precision_is_thirty(FloatEnumType $enumType): void
    {
        $defaultLength = $enumType->getDecimalPrecision($this->getAbstractPlatform());
        self::assertSame(30, $defaultLength);
    }

    /**
     * @param FloatEnumType $enumType
     * @test
     * @depends I_can_get_instance
     */
    public function enum_as_database_value_is_float_value_of_that_enum(FloatEnumType $enumType): void
    {
        $enum = $this->createEnum($value = 12345.67859);
        self::assertSame($value, $enumType->convertToDatabaseValue($enum, $this->getAbstractPlatform()));
    }

    /**
     * @param $value
     * @return \Mockery\MockInterface|ScalarEnumInterface
     */
    private function createEnum($value): ScalarEnumInterface
    {
        $enum = $this->mockery(ScalarEnumInterface::class);
        $enum->shouldReceive('getValue')
            ->once()
            ->andReturn($value);

        return $enum;
    }

    /**
     * CONVERSIONS TO PHP VALUE
     */

    /**
     * @param FloatEnumType $enumType
     * @test
     * @depends I_can_get_instance
     */
    public function float_to_php_value_gives_enum_with_that_float(FloatEnumType $enumType): void
    {
        $enum = $enumType->convertToPHPValue($float = 12345.67859, $this->getAbstractPlatform());
        self::assertInstanceOf($this->getRegisteredEnumClass(), $enum);
        self::assertSame($float, $enum->getValue());
        self::assertSame((string)$float, (string)$enum);
    }

    /**
     * @return \Doctrineum\Float\FloatEnum|string
     */
    protected function getRegisteredEnumClass()
    {
        return FloatEnum::class;
    }

    /**
     * @param FloatEnumType $enumType
     * @test
     * @depends I_can_get_instance
     */
    public function string_float_to_php_value_gives_enum_with_that_float(FloatEnumType $enumType): void
    {
        $value = $enumType->convertToPHPValue($stringFloat = '12345.67859', $this->getAbstractPlatform());
        self::assertInstanceOf($this->getRegisteredEnumClass(), $value);
        self::assertSame((float)$stringFloat, $value->getValue());
    }

    /**
     * @param $nonNumericValue
     * @test
     * @expectedException \Doctrineum\Float\Exceptions\UnexpectedValueToConvert
     * @dataProvider provideNonNumericNonNullValue
     */
    public function I_can_not_convert_non_numeric_to_enum_value($nonNumericValue)
    {
        $enumType = Type::getType($this->getExpectedTypeName());
        $enumType->convertToPHPValue($nonNumericValue, $this->getAbstractPlatform());
    }

    public function provideNonNumericNonNullValue(): array
    {
        return [
            [''],
            [[]],
            [tmpfile()],
            [new \stdClass()],
            [function () {
                return 123;
            }],
        ];
    }

    /**
     * @test
     */
    public function I_get_null_if_fetched_from_database(): void
    {
        $enumType = Type::getType($this->getExpectedTypeName());
        self::assertNull($enumType->convertToPHPValue(null, $this->getAbstractPlatform()));
    }

    /**
     * @param FloatEnumType $enumType
     * @test
     * @depends I_can_get_instance
     */
    public function integer_to_php_value_gives_enum_with_float(FloatEnumType $enumType): void
    {
        $enum = $enumType->convertToPHPValue($integer = 12345, $this->getAbstractPlatform());
        self::assertInstanceOf($this->getRegisteredEnumClass(), $enum);
        self::assertSame(12345.0, $enum->getValue());
        self::assertSame((float)$integer, $enum->getValue());
        self::assertSame((string)(float)$integer, (string)$enum);
    }

    /**
     * @param FloatEnumType $enumType
     * @test
     * @depends I_can_get_instance
     */
    public function zero_integer_to_php_value_gives_enum_with_float(FloatEnumType $enumType): void
    {
        $enum = $enumType->convertToPHPValue($zeroInteger = 0, $this->getAbstractPlatform());
        self::assertInstanceOf($this->getRegisteredEnumClass(), $enum);
        self::assertSame(0.0, $enum->getValue());
        self::assertSame((float)$zeroInteger, $enum->getValue());
        self::assertSame((string)(float)$zeroInteger, (string)$enum);
    }

    /**
     * @param FloatEnumType $enumType
     * @test
     * @depends I_can_get_instance
     */
    public function false_to_php_value_is_zero(FloatEnumType $enumType): void
    {
        $enum = $enumType->convertToPHPValue($false = false, $this->getAbstractPlatform());
        self::assertSame(0.0, $enum->getValue());
        self::assertSame((float)$false, $enum->getValue());
        self::assertSame((string)(float)$false, (string)$enum);
    }

    /**
     * @param FloatEnumType $enumType
     * @test
     * @depends I_can_get_instance
     */
    public function true_to_php_value_is_one(FloatEnumType $enumType)
    {
        $enum = $enumType->convertToPHPValue($true = true, $this->getAbstractPlatform());
        self::assertSame(1.0, $enum->getValue());
        self::assertSame((float)$true, $enum->getValue());
        self::assertSame((string)(float)$true, (string)$enum);
    }

    /**
     * subtype tests
     */

    /**
     * @param FloatEnumType $enumType
     * @return FloatEnumType
     * @test
     * @depends I_can_get_instance
     */
    public function can_register_subtype(FloatEnumType $enumType): FloatEnumType
    {
        self::assertTrue($enumType::addSubTypeEnum(TestSubTypeFloatEnum::class, '~foo~'));
        self::assertTrue($enumType::hasSubTypeEnum(TestSubTypeFloatEnum::class));

        return $enumType;
    }

    /**
     * @param FloatEnumType $enumType
     * @test
     * @depends can_register_subtype
     */
    public function can_unregister_subtype(FloatEnumType $enumType): void
    {
        /**
         * The subtype is unregistered because of tearDown clean up
         *
         * @see FloatEnumTypeTestTrait::tearDown
         */
        self::assertFalse($enumType::hasSubTypeEnum(TestSubTypeFloatEnum::class), 'Subtype should not be registered yet');
        self::assertTrue($enumType::addSubTypeEnum(TestSubTypeFloatEnum::class, '~foo~'));
        self::assertTrue($enumType::removeSubTypeEnum(TestSubTypeFloatEnum::class));
        self::assertFalse($enumType::hasSubTypeEnum(TestSubTypeFloatEnum::class));
    }

    /**
     * @param FloatEnumType $enumType
     * @test
     * @depends can_register_subtype
     */
    public function subtype_returns_proper_enum(FloatEnumType $enumType): void
    {
        $enumType::addSubTypeEnum(TestSubTypeFloatEnum::class, $regexp = '~45.6~');
        self::assertTrue($enumType::hasSubTypeEnum(TestSubTypeFloatEnum::class));
        /** @var AbstractPlatform $abstractPlatform */
        $abstractPlatform = $this->mockery(AbstractPlatform::class);
        $matchingValueToConvert = 12345.6789;
        self::assertRegExp($regexp, (string)$matchingValueToConvert);
        /**
         * Used TestSubtype returns as an "enum" the given value, which is $valueToConvert in this case,
         *
         * @see \Doctrineum\Tests\Scalar\TestSubtype::getEnum
         */
        $enumFromSubType = $enumType->convertToPHPValue($matchingValueToConvert, $abstractPlatform);
        self::assertInstanceOf(TestSubTypeFloatEnum::class, $enumFromSubType);
        self::assertSame((string)$matchingValueToConvert, (string)$enumFromSubType);
    }

    /**
     * @param FloatEnumType $enumType
     * @test
     * @depends can_register_subtype
     */
    public function default_enum_is_given_if_subtype_does_not_match(FloatEnumType $enumType): void
    {
        $enumType::addSubTypeEnum(TestSubTypeFloatEnum::class, $regexp = '~45.6~');
        self::assertTrue($enumType::hasSubTypeEnum(TestSubTypeFloatEnum::class));
        /** @var AbstractPlatform $abstractPlatform */
        $abstractPlatform = $this->mockery(AbstractPlatform::class);
        $nonMatchingValueToConvert = 9999.9999;
        self::assertNotRegExp($regexp, (string)$nonMatchingValueToConvert);
        /**
         * Used TestSubtype returns as an "enum" the given value, which is $valueToConvert in this case,
         *
         * @see \Doctrineum\Tests\Scalar\TestSubtype::getEnum
         */
        $enum = $enumType->convertToPHPValue($nonMatchingValueToConvert, $abstractPlatform);
        self::assertNotSame($nonMatchingValueToConvert, $enum);
        self::assertInstanceOf(FloatEnumInterface::class, $enum);
        self::assertSame((string)$nonMatchingValueToConvert, (string)$enum);
    }

    /**
     * @param FloatEnumType $enumType
     * @test
     * @depends I_can_get_instance
     * @expectedException \Doctrineum\Scalar\Exceptions\SubTypeEnumIsAlreadyRegistered
     */
    public function registering_same_subtype_again_throws_exception(FloatEnumType $enumType): void
    {
        self::assertFalse($enumType::hasSubTypeEnum(TestSubTypeFloatEnum::class));
        self::assertTrue($enumType::addSubTypeEnum(TestSubTypeFloatEnum::class, '~foo~'));
        // registering twice - should thrown an exception
        $enumType::addSubTypeEnum(TestSubTypeFloatEnum::class, '~foo~');
    }

    /**
     * @param FloatEnumType $enumType
     * @test
     * @depends I_can_get_instance
     * @expectedException \Doctrineum\Scalar\Exceptions\SubTypeEnumClassNotFound
     */
    public function registering_non_existing_subtype_class_throws_exception(FloatEnumType $enumType): void
    {
        $enumType::addSubTypeEnum('NonExistingClassName', '~foo~');
    }

    /**
     * @param FloatEnumType $enumType
     * @test
     * @depends I_can_get_instance
     * @expectedException \Doctrineum\Scalar\Exceptions\SubTypeEnumHasToBeEnum
     */
    public function registering_subtype_class_without_proper_method_throws_exception(FloatEnumType $enumType): void
    {
        $enumType::addSubTypeEnum(\stdClass::class, '~foo~');
    }

    /**
     * @param FloatEnumType $enumType
     * @test
     * @depends I_can_get_instance
     * @expectedException \Doctrineum\Scalar\Exceptions\InvalidRegexpFormat
     * @expectedExceptionMessage The given regexp is not enclosed by same delimiters and therefore is not valid: 'foo~'
     */
    public function registering_subtype_with_invalid_regexp_throws_exception(FloatEnumType $enumType): void
    {
        $enumType::addSubTypeEnum(TestSubTypeFloatEnum::class, 'foo~' /* missing opening delimiter */);
    }

    /**
     * @test
     */
    public function different_types_with_same_subtype_regexp_distinguish_them(): void
    {
        /** @var FloatEnumType $enumTypeClass */
        $enumTypeClass = $this->getTypeClass();
        if ($enumTypeClass::hasSubTypeEnum(TestSubTypeFloatEnum::class)) {
            $enumTypeClass::removeSubTypeEnum(TestSubTypeFloatEnum::class);
        }
        $enumTypeClass::addSubTypeEnum(TestSubTypeFloatEnum::class, $regexp = '~[4-6]+~');

        TestAnotherFloatEnumType::registerSelf();
        if (TestAnotherFloatEnumType::hasSubTypeEnum(TestAnotherSubTypeFloatEnum::class)) {
            TestAnotherFloatEnumType::removeSubTypeEnum(TestAnotherSubTypeFloatEnum::class);
        }
        // regexp is same, sub-type is not
        TestAnotherFloatEnumType::addSubTypeEnum(TestAnotherSubTypeFloatEnum::class, $regexp);

        $value = 345.678;
        self::assertRegExp($regexp, (string)$value);

        $enumType = Type::getType($this->getExpectedTypeName());
        $enumSubType = $enumType->convertToPHPValue($value, $this->getPlatform());
        self::assertInstanceOf(TestSubTypeFloatEnum::class, $enumSubType);
        self::assertSame("$value", "$enumSubType");

        $anotherEnumType = Type::getType($this->getExpectedTypeName(TestAnotherFloatEnumType::class));
        $anotherEnumSubType = $anotherEnumType->convertToPHPValue($value, $this->getPlatform());
        self::assertInstanceOf(TestSubTypeFloatEnum::class, $enumSubType);
        self::assertSame((string)$value, (string)$anotherEnumSubType);

        // registered sub-types are different, although regexp is the same - let's test if they are kept separately
        self::assertNotSame($enumSubType, $anotherEnumSubType);
    }

    /**
     * @return AbstractPlatform|\Mockery\MockInterface
     */
    protected function getPlatform()
    {
        return $this->mockery(AbstractPlatform::class);
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
    public const TEST_ANOTHER_FLOAT_ENUM = 'test_another_float_enum';

    public function getName(): string
    {
        return self::TEST_ANOTHER_FLOAT_ENUM;
    }
}