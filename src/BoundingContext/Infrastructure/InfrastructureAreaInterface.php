<?php

declare(strict_types=1);

namespace SoureCode\DomainDrivenDesign\BoundingContext\Infrastructure;

use SoureCode\DomainDrivenDesign\BoundingContext\Infrastructure\Repository\RepositoryAreaInterface;

interface InfrastructureAreaInterface extends RepositoryAreaInterface
{
    public function repository(): RepositoryAreaInterface;
}
