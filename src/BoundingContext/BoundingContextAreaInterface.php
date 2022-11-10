<?php

declare(strict_types=1);

namespace SoureCode\DomainDrivenDesign\BoundingContext;

use SoureCode\DomainDrivenDesign\Area\SubAreaInterface;
use SoureCode\DomainDrivenDesign\BoundingContext\Domain\DomainAreaInterface;
use SoureCode\DomainDrivenDesign\BoundingContext\Infrastructure\InfrastructureAreaInterface;

interface BoundingContextAreaInterface extends SubAreaInterface
{
    public function domain(): DomainAreaInterface;

    public function infrastructure(): InfrastructureAreaInterface;
}
