<?php

declare(strict_types=1);

namespace SoureCode\DomainDrivenDesign\Factory;

use SoureCode\DomainDrivenDesign\Area\AreaInterface;
use SoureCode\DomainDrivenDesign\Domain\ValueObjectArea;

class ValueObjectAreaFactory
{
    private ValueObjectFactory $valueObjectFactory;

    public function __construct(ValueObjectFactory $valueObjectFactory)
    {
        $this->valueObjectFactory = $valueObjectFactory;
    }

    public function create(AreaInterface $area, string $name): ValueObjectArea
    {
        return new ValueObjectArea($this->valueObjectFactory, $area, $name);
    }
}
