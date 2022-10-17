<?php

declare(strict_types=1);

namespace SoureCode\DomainDrivenDesign;

use SoureCode\DomainDrivenDesign\Area\AbstractArea;
use SoureCode\DomainDrivenDesign\BoundingContext\BoundingContextArea;
use SoureCode\DomainDrivenDesign\Factory\BoundingContextAreaFactory;
use SoureCode\PhpObjectModel\ValueObject\NamespaceName;

class DomainDriveDesign extends AbstractArea
{
    private BoundingContextAreaFactory $boundingContextAreaFactory;

    public function __construct(
        BoundingContextAreaFactory $boundingContextAreaFactory,
        string $directory,
        string|NamespaceName $namespace
    ) {
        $this->boundingContextAreaFactory = $boundingContextAreaFactory;

        parent::__construct($directory, $namespace);
    }

    /**
     * @return BoundingContextArea[]
     */
    public function getBoundingContexts(): array
    {
        return $this->getSubAreas($this->boundingContextAreaFactory->create(...));
    }

    public function getBoundingContext(string $name): BoundingContextArea
    {
        return $this->getSubArea($name, $this->boundingContextAreaFactory->create(...));
    }

    public function hasBoundingContext(string $name): bool
    {
        return $this->hasSubArea($name);
    }

    public function createBoundingContext(string $name): BoundingContextArea
    {
        return $this->createSubArea($name, $this->boundingContextAreaFactory->create(...));
    }
}
