<?php

declare(strict_types=1);

namespace SoureCode\DomainDrivenDesign\ValueObject;

use SoureCode\DomainDrivenDesign\Area\AreaInterface;

interface ValueObjectFactoryInterface
{
    public function create(AreaInterface $area, string $name): ValueObject;
}
