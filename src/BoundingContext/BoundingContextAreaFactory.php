<?php

declare(strict_types=1);

namespace SoureCode\DomainDrivenDesign\BoundingContext;

use SoureCode\DomainDrivenDesign\Area\AreaInterface;
use SoureCode\DomainDrivenDesign\Domain\DomainAreaFactory;

class BoundingContextAreaFactory
{
    private DomainAreaFactory $domainAreaFactory;

    public function __construct(DomainAreaFactory $domainAreaFactory)
    {
        $this->domainAreaFactory = $domainAreaFactory;
    }

    public function create(AreaInterface $area, string $name): BoundingContextArea
    {
        return new BoundingContextArea($this->domainAreaFactory, $area, $name);
    }
}
