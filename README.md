Doctrine Float enum
=====================

[![Build Status](https://travis-ci.org/jaroslavtyc/doctrineum-float.svg?branch=master)](https://travis-ci.org/jaroslavtyc/doctrineum-float)
[![Test Coverage](https://codeclimate.com/github/jaroslavtyc/doctrineum-float/badges/coverage.svg)](https://codeclimate.com/github/jaroslavtyc/doctrineum-float/coverage)
[![License](https://poser.pugx.org/doctrineum/float/license)](https://packagist.org/packages/doctrineum/float)

## About
Adds [Enum](http://en.wikipedia.org/wiki/Enumerated_type) to [Doctrine ORM](http://www.doctrine-project.org/)
(can be used as a `@Column(type="float_enum")`).

##Usage

```php
<?php

use Doctrine\ORM\Mapping as ORM;
use Doctrineum\Float\FloatEnum;

/**
 * @ORM\Entity()
 */
class Person
{
    /**
     * @var int
     * @ORM\Id() @ORM\GeneratedValue(strategy="AUTO") @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var FloatEnum
     * @ORM\Column(type="float_enum")
     */
    private $height;
    
    public function __construct(FloatEnum $height)
    {
        $this->height = $height;
    }

    /**
     * @return FloatEnum
     */
    public function getHeight()
    {
        return $this->height;
    }
}

$tallMan = new Person(FloatEnum::getEnum(197.45));

/** @var \Doctrine\ORM\EntityManager $entityManager */
$entityManager->persist($tallMan);
$entityManager->flush();
$entityManager->clear();

/** @var Person[] $persons */
$canSitOnFrontSeat = $entityManager->createQuery(
    "SELECT p FROM Person p WHERE p.height >= 140"
)->getResult();

var_dump($canSitOnFrontSeat[0]->getHeight()->getValue()); // 197.45;
```

##Installation

Add it to your list of Composer dependencies (or by manual edit your composer.json, the `require` section)

```sh
composer require jaroslavtyc/doctrineum-float
```

## Doctrine integration

Register new DBAL type:

```php
<?php

use Doctrineum\Float\FloatEnumType;

FloatEnumType::registerSelf();
```

When using Symfony with Doctrine you can do the same as above by configuration:

```yaml
# app/config/config.yml

# Doctrine Configuration
doctrine:
    dbal:
        # ...
        mapping_types:
            float_enum: float_enum
        types:
            float_enum: Doctrineum\Float\FloatEnumType
```
