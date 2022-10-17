<?php

declare(strict_types=1);

namespace SoureCode\DomainDrivenDesign\Factory;

use SoureCode\DomainDrivenDesign\Area\AreaInterface;
use SoureCode\DomainDrivenDesign\Domain\ModelArea;

class ModelAreaFactory
{
    private ModelFactory $modelFactory;

    public function __construct(ModelFactory $modelFactory)
    {
        $this->modelFactory = $modelFactory;
    }

    public function create(AreaInterface $area, string $name): ModelArea
    {
        return new ModelArea($this->modelFactory, $area, $name);
    }
}
