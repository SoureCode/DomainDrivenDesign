<?php

declare(strict_types=1);

namespace SoureCode\DomainDrivenDesign\BoundingContext\Domain\ValueObject;

use SoureCode\DomainDrivenDesign\Area\AreaInterface;

interface ValueObjectFactoryInterface
{
    public function create(AreaInterface $area, string $name): ValueObjectInterface;
}
