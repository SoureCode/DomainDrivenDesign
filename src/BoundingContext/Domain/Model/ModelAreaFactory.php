<?php

declare(strict_types=1);

namespace SoureCode\DomainDrivenDesign\BoundingContext\Domain\Model;

use SoureCode\DomainDrivenDesign\Area\AreaInterface;

class ModelAreaFactory
{
    /**
     * @var class-string<ModelAreaInterface>
     */
    private string $className;

    private ModelFactoryInterface $modelFactory;

    /**
     * @param class-string<ModelAreaInterface> $className
     */
    public function __construct(string $className, ModelFactoryInterface $modelFactory)
    {
        $this->className = $className;
        $this->modelFactory = $modelFactory;
    }

    public function create(AreaInterface $area, string $name): ModelAreaInterface
    {
        return new ($this->className)($this->modelFactory, $area, $name);
    }
}
