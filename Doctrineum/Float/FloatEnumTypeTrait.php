<?php
namespace Doctrineum\Float;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrineum\Scalar\EnumInterface;
use Granam\Float\Tools\ToFloat;

/**
 * @method float convertToDatabaseValue(EnumInterface $enumValue, AbstractPlatform $platform)
 * @see \Doctrineum\Scalar\EnumType::convertToDatabaseValue
 */
trait FloatEnumTypeTrait
{

    /**
     * The PHP float is saved as SQL decimal, therefore exactly as given (SQL float is rounded, therefore changed often).
     * Gets the SQL declaration snippet for a field of this type.
     *
     * @param array $fieldDeclaration The field declaration.
     * @param \Doctrine\DBAL\Platforms\AbstractPlatform $platform The currently used database platform.
     *
     * @return string
     */
    public function getSQLDeclaration(
        /** @noinspection PhpUnusedParameterInspection */
        array $fieldDeclaration,
        AbstractPlatform $platform
    )
    {
        return "DECIMAL({$this->getDefaultLength($platform)},{$this->getDecimalPrecision($platform)})";
    }

    /**
     * @param AbstractPlatform $platform
     * @return int
     */
    public function getDefaultLength(
        /** @noinspection PhpUnusedParameterInspection */
        AbstractPlatform $platform
    )
    {
        return 65;
    }

    /**
     * @param AbstractPlatform $platform
     * @return int
     */
    public function getDecimalPrecision(
        /** @noinspection PhpUnusedParameterInspection */
        AbstractPlatform $platform
    )
    {
        return 30;
    }
    /**
     * @param mixed $value
     */
    protected function checkValueToConvert($value)
    {
        try {
            // Uses side effect of the conversion - the checks
            ToFloat::tofloat($value);
        } catch (\Granam\Float\Tools\Exceptions\WrongParameterType $exception) {
            // wrapping exception by a local one
            throw new \Doctrineum\Float\Exceptions\UnexpectedValueToConvert($exception->getMessage(), $exception->getCode(), $exception);
        }
    }
}
