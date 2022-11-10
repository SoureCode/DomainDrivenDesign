<?php

declare(strict_types=1);

namespace SoureCode\DomainDrivenDesign\BoundingContext\Domain\Repository;

use SoureCode\DomainDrivenDesign\Area\AreaInterface;

interface RepositoryFactoryInterface
{
    public function create(AreaInterface $area, string $name): RepositoryInterface;
}
