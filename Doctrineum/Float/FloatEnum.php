<?php
namespace Doctrineum\Float;

use Doctrineum\Scalar\Enum;

/**
 * @method float getEnumValue()
 */
class FloatEnum extends Enum
{

    use ConvertToFloatEnumFinalValueTrait;

}
