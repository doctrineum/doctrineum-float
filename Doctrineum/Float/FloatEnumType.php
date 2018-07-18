<?php
declare(strict_types=1);

namespace Doctrineum\Float;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrineum\Scalar\ScalarEnumInterface;
use Doctrineum\Scalar\ScalarEnumType;
use Granam\Float\Tools\ToFloat;

/**
 * @method float convertToDatabaseValue(ScalarEnumInterface $enumValue, AbstractPlatform $platform)
 * @method FloatEnumInterface convertToPHPValue($value, AbstractPlatform $platform)
 */
class FloatEnumType extends ScalarEnumType
{
    public const FLOAT_ENUM = 'float_enum';

    public function getName(): string
    {
        return self::FLOAT_ENUM;
    }

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
    ): string
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
    ): int
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
    ): int
    {
        return 30;
    }

    /**
     * @see \Doctrineum\Scalar\ScalarEnumType::convertToPHPValue for usage
     *
     * @param string $enumValue
     * @return FloatEnum
     * @throws \Doctrineum\Float\Exceptions\UnexpectedValueToConvert
     */
    protected function convertToEnum($enumValue): ScalarEnumInterface
    {
        return parent::convertToEnum($this->toFloat($enumValue));
    }

    /**
     * @param mixed $value
     * @return float
     * @throws \Doctrineum\Float\Exceptions\UnexpectedValueToConvert
     */
    protected function toFloat($value): float
    {
        try {
            // Uses side effect of the conversion - the checks
            return ToFloat::toFloat($value, true /* strict */);
        } catch (\Granam\Float\Tools\Exceptions\WrongParameterType $exception) {
            // wrapping exception by a local one
            throw new Exceptions\UnexpectedValueToConvert($exception->getMessage(), $exception->getCode(), $exception);
        }
    }
}
