[![Build Status](https://travis-ci.org/jaroslavtyc/doctrineum-float.svg?branch=master)](https://travis-ci.org/jaroslavtyc/doctrineum-float)
[![Test Coverage](https://codeclimate.com/github/jaroslavtyc/doctrineum-float/badges/coverage.svg)](https://codeclimate.com/github/jaroslavtyc/doctrineum-float/coverage)
[![License](https://poser.pugx.org/doctrineum/float/license)](https://packagist.org/packages/doctrineum/float)

# About
[Doctrine](http://www.doctrine-project.org/) [enum](http://en.wikipedia.org/wiki/Enumerated_type) allowing floats only.

### Example
```php
$floatEnum = FloatEnum::getEnum(12345.6789);
(int)(string)$floatEnum === $floatEnum->getValue() === 12345.6789; // true

// correct, string with float is allowed
$floatEnum = FloatEnum::getEnum('12345.6789');

// correct - white characters are trimmed, the rest is pure float
$floatEnum = FloatEnum::getEnum('  12.34     ');

// correct - integer can be easily converted to a float - and it is
FloatEnum::getEnum(12);

// correct - the leading numbers are converted to float = 12.0, same way as floatval() should do
FloatEnum::getEnum('12foo');

// correct - is converted to 0.0, same way as floatval() should do
FloatEnum::getEnum('');

// cause exception - only scalar values, null, or to string object can be cast to float
FloatEnum::getEnum(array(1.5));

// cause exception - null can not be used for any enum, use null directly for the column value instead
FloatEnum::getEnum(null)
```

# Doctrine integration
For details about new Doctrine type registration, see the parent project [Doctrineum](https://github.com/jaroslavtyc/doctrineum).
