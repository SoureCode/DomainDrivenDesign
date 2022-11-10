<?php

declare(strict_types=1);

namespace SoureCode\DomainDrivenDesign\BoundingContext;

use SoureCode\DomainDrivenDesign\Area\AreaInterface;
use SoureCode\DomainDrivenDesign\BoundingContext\Domain\DomainAreaFactory;
use SoureCode\DomainDrivenDesign\BoundingContext\Infrastructure\InfrastructureAreaFactory;

class BoundingContextAreaFactory
{
    /**
     * @var class-string<BoundingContextAreaInterface>
     */
    private string $className;

    private DomainAreaFactory $domainAreaFactory;

    private InfrastructureAreaFactory $infrastructureAreaFactory;

    /**
     * @param class-string<BoundingContextAreaInterface> $className
     */
    public function __construct(
        string $className,
        DomainAreaFactory $domainAreaFactory,
        InfrastructureAreaFactory $infrastructureAreaFactory
    ) {
        $this->className = $className;
        $this->domainAreaFactory = $domainAreaFactory;
        $this->infrastructureAreaFactory = $infrastructureAreaFactory;
    }

    public function create(AreaInterface $area, string $name): BoundingContextAreaInterface
    {
        return new ($this->className)($this->domainAreaFactory, $this->infrastructureAreaFactory, $area, $name);
    }
}
