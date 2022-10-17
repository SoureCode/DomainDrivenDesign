<?php

declare(strict_types=1);

namespace SoureCode\DomainDrivenDesign\Factory;

use SoureCode\DomainDrivenDesign\Area\AreaInterface;
use SoureCode\DomainDrivenDesign\Domain\DomainArea;

class DomainAreaFactory
{
    private ModelAreaFactory $modelAreaFactory;

    private ValueObjectAreaFactory $valueObjectAreaFactory;

    public function __construct(ModelAreaFactory $modelAreaFactory, ValueObjectAreaFactory $valueObjectAreaFactory)
    {
        $this->modelAreaFactory = $modelAreaFactory;
        $this->valueObjectAreaFactory = $valueObjectAreaFactory;
    }

    public function create(AreaInterface $area, string $name): DomainArea
    {
        return new DomainArea($this->modelAreaFactory, $this->valueObjectAreaFactory, $area, $name);
    }
}
