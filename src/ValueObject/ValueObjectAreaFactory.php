<?php

declare(strict_types=1);

namespace SoureCode\DomainDrivenDesign\ValueObject;

use SoureCode\DomainDrivenDesign\Area\AreaInterface;

class ValueObjectAreaFactory
{
    private ValueObjectFactoryInterface $valueObjectFactory;

    public function __construct(ValueObjectFactoryInterface $valueObjectFactory)
    {
        $this->valueObjectFactory = $valueObjectFactory;
    }

    public function create(AreaInterface $area, string $name): ValueObjectArea
    {
        return new ValueObjectArea($this->valueObjectFactory, $area, $name);
    }
}
