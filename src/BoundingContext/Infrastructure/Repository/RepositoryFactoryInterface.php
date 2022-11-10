<?php

declare(strict_types=1);

namespace SoureCode\DomainDrivenDesign\BoundingContext\Infrastructure\Repository;

use SoureCode\DomainDrivenDesign\Area\AreaInterface;

interface RepositoryFactoryInterface
{
    public function create(AreaInterface $area, string $name): RepositoryInterface;
}
