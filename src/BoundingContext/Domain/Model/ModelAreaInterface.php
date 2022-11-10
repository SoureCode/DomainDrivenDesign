<?php

declare(strict_types=1);

namespace SoureCode\DomainDrivenDesign\BoundingContext\Domain\Model;

use SoureCode\DomainDrivenDesign\Area\SubAreaInterface;

interface ModelAreaInterface extends SubAreaInterface
{
    /**
     * @return ModelInterface[]
     */
    public function getModels(): array;

    public function getModel(string $name): ModelInterface;

    public function hasModel(string $name): bool;

    public function createModel(string $name): ModelInterface;
}
