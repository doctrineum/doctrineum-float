<?php
namespace Doctrineum\Float;

use Doctrineum\Scalar\EnumType;

/**
 * Class EnumType
 * @package Doctrineum
 *
 * @method static FloatEnumType getType($name),
 * @see Type::getType
 */
class FloatEnumType extends EnumType
{
    use FloatEnumTypeTrait;

    const FLOAT_ENUM = 'float_enum';

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
