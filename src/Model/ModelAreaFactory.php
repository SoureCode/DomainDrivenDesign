<?php

declare(strict_types=1);

namespace SoureCode\DomainDrivenDesign\Model;

use SoureCode\DomainDrivenDesign\Area\AreaInterface;

class ModelAreaFactory
{
    private ModelFactoryInterface $modelFactory;

    public function __construct(ModelFactoryInterface $modelFactory)
    {
        $this->modelFactory = $modelFactory;
    }

    public function create(AreaInterface $area, string $name): ModelArea
    {
        return new ModelArea($this->modelFactory, $area, $name);
    }
}
