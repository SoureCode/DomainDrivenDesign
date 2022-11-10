<?php

declare(strict_types=1);

namespace SoureCode\DomainDrivenDesign\BoundingContext\Domain\Model;

use SoureCode\DomainDrivenDesign\Area\AreaInterface;

interface ModelFactoryInterface
{
    public function create(AreaInterface $area, string $name): ModelInterface;
}
