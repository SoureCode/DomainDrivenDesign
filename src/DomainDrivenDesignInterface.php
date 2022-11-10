<?php

declare(strict_types=1);

namespace SoureCode\DomainDrivenDesign;

use SoureCode\DomainDrivenDesign\Area\AreaInterface;
use SoureCode\DomainDrivenDesign\BoundingContext\BoundingContextAreaInterface;

interface DomainDrivenDesignInterface extends AreaInterface
{
    /**
     * @return BoundingContextAreaInterface[]
     */
    public function getBoundingContexts(): array;

    public function getBoundingContext(string $name): BoundingContextAreaInterface;

    public function hasBoundingContext(string $name): bool;

    public function createBoundingContext(string $name): BoundingContextAreaInterface;
}
