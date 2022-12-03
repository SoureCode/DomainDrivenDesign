<?php

declare(strict_types=1);

namespace SoureCode\DomainDrivenDesign\BoundingContext\Domain;

use SoureCode\DomainDrivenDesign\Area\AreaInterface;
use SoureCode\DomainDrivenDesign\BoundingContext\Domain\Model\ModelAreaFactory;
use SoureCode\DomainDrivenDesign\BoundingContext\Domain\Repository\RepositoryInterfaceAreaFactory;
use SoureCode\DomainDrivenDesign\BoundingContext\Domain\ValueObject\ValueObjectAreaFactory;

class DomainAreaFactory
{
    /**
     * @var class-string<DomainAreaInterface>
     */
    private string $className;

    private ModelAreaFactory $modelAreaFactory;

    private ValueObjectAreaFactory $valueObjectAreaFactory;

    private RepositoryInterfaceAreaFactory $repositoryAreaFactory;

    /**
     * @param class-string<DomainAreaInterface> $className
     */
    public function __construct(
        string $className,
        ModelAreaFactory $modelAreaFactory,
        ValueObjectAreaFactory $valueObjectAreaFactory,
        RepositoryInterfaceAreaFactory $repositoryAreaFactory
    ) {
        $this->className = $className;
        $this->modelAreaFactory = $modelAreaFactory;
        $this->valueObjectAreaFactory = $valueObjectAreaFactory;
        $this->repositoryAreaFactory = $repositoryAreaFactory;
    }

    public function create(AreaInterface $area, string $name): DomainAreaInterface
    {
        return new ($this->className)(
            $this->modelAreaFactory,
            $this->valueObjectAreaFactory,
            $this->repositoryAreaFactory,
            $area,
            $name
        );
    }
}
