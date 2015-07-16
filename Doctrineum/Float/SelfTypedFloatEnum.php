<?php
namespace Doctrineum\Float;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrineum\Scalar\EnumInterface;
use Doctrineum\Scalar\SelfTypedEnum;

/**
 * @method static SelfTypedFloatEnum getType($name),
 * @see SelfTypedEnum::getType or the origin
 * @see Type::getType
 *
 * @method static SelfTypedFloatEnum getEnum($enumValue),
 * @see SelfTypedEnum::getEnum
 */
class SelfTypedFloatEnum extends SelfTypedEnum
{
    use FloatEnumTypeTrait;
    use ConvertToFloatEnumFinalValueTrait;

    /**
     * Its not directly used this library - the exactly same value is generated and used by
     * @see \Doctrineum\Scalar\SelfTypedEnum::getTypeName
     *
     * This constant exists to follow Doctrine type conventions.
     */
    const SELF_TYPED_FLOAT_ENUM = 'self_typed_float_enum';

    /**
     * Convert enum instance to database string (or null) value
     *
     * @param EnumInterface $value
     * @param \Doctrine\DBAL\Platforms\AbstractPlatform $platform
     *
     * @throws Exceptions\UnexpectedValueToDatabaseValue
     * @return string|null
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        $floatNumber = parent::convertToDatabaseValue($value, $platform);
        $this->checkAgainstSqlLength($floatNumber, $platform);

        return $floatNumber;
    }

    /**
     * @param float $floatNumber
     * @param \Doctrine\DBAL\Platforms\AbstractPlatform $platform
     */
    private function checkAgainstSqlLength($floatNumber, AbstractPlatform $platform)
    {
        if ($this->getDigitsCount($floatNumber) > self::getDefaultLength($platform)) {
            throw new Exceptions\ValueLengthExceeded(
                "Maximal float length to store in database is " . self::getDefaultLength($platform)
                . ", got value $floatNumber of length {$this->getDigitsCount($floatNumber)}"
            );
        }

        if ($this->getDecimalDigitsCount($floatNumber) > self::getDecimalPrecision($platform)) {
            throw new Exceptions\InsufficientPrecision(
                "Maximal decimal precision is " . self::getDecimalPrecision($platform)
                . ", got value $floatNumber of decimal digits count {$this->getDecimalDigitsCount($floatNumber)}"
            );
        }
    }

    /**
     * @param float $floatValue
     * @return int
     */
    private function getDigitsCount($floatValue)
    {
        return strlen(str_replace('.', '', "$floatValue"));
    }

    /**
     * @param float $floatValue
     * @return int
     */
    private function getDecimalDigitsCount($floatValue)
    {
        preg_match('~\.(?<decimalValue>\d+)$~', "$floatValue", $matches);

        return isset($matches['decimalValue'])
            ? strlen($matches['decimalValue'])
            : 0;
    }

    /**
     * @see \Doctrineum\Scalar\EnumType::convertToPHPValue for usage
     *
     * @param string $enumValue
     * @return FloatEnum
     */
    protected function convertToEnum($enumValue)
    {
        $this->checkValueToConvert($enumValue);

        return parent::convertToEnum($enumValue);
    }
}
