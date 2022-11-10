<?php

declare(strict_types=1);

namespace SoureCode\DomainDrivenDesign\BoundingContext\Domain\ValueObject;

use SoureCode\DomainDrivenDesign\Area\SubAreaInterface;

interface ValueObjectAreaInterface extends SubAreaInterface
{
    /**
     * @return ValueObjectInterface[]
     */
    public function getValueObjects(): array;

    public function getValueObject(string $name): ValueObjectInterface;

    public function hasValueObject(string $name): bool;

    public function createValueObject(string $name): ValueObjectInterface;
}
