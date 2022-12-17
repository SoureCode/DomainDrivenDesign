<?php

declare(strict_types=1);

namespace SoureCode\DomainDrivenDesign;

use SoureCode\DomainDrivenDesign\Area\AbstractArea;
use SoureCode\DomainDrivenDesign\BoundingContext\BoundingContextAreaFactory;
use SoureCode\DomainDrivenDesign\BoundingContext\BoundingContextAreaInterface;
use SoureCode\PhpObjectModel\ValueObject\NamespaceName;

/**
 * @extends AbstractArea<BoundingContextAreaInterface>
 */
class DomainDrivenDesign extends AbstractArea implements DomainDrivenDesignInterface
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
     * @return BoundingContextAreaInterface[]
     */
    public function getBoundingContexts(): array
    {
        return $this->getSubAreas($this->boundingContextAreaFactory->create(...));
    }

    public function getBoundingContext(string $name): BoundingContextAreaInterface
    {
        return $this->getSubArea($name, $this->boundingContextAreaFactory->create(...));
    }

    public function hasBoundingContext(string $name): bool
    {
        return $this->hasSubArea($name);
    }

    public function createBoundingContext(string $name): BoundingContextAreaInterface
    {
        return $this->createSubArea($name, $this->boundingContextAreaFactory->create(...));
    }
}
