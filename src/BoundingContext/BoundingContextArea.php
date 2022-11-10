<?php

declare(strict_types=1);

namespace SoureCode\DomainDrivenDesign\BoundingContext;

use SoureCode\DomainDrivenDesign\Area\AbstractSubArea;
use SoureCode\DomainDrivenDesign\Area\AreaInterface;
use SoureCode\DomainDrivenDesign\Area\SubAreaInterface;
use SoureCode\DomainDrivenDesign\BoundingContext\Domain\DomainAreaFactory;
use SoureCode\DomainDrivenDesign\BoundingContext\Domain\DomainAreaInterface;
use SoureCode\DomainDrivenDesign\BoundingContext\Infrastructure\InfrastructureAreaFactory;
use SoureCode\DomainDrivenDesign\BoundingContext\Infrastructure\InfrastructureAreaInterface;

/**
 * @extends AbstractSubArea<SubAreaInterface>
 */
class BoundingContextArea extends AbstractSubArea implements BoundingContextAreaInterface
{
    private DomainAreaFactory $domainAreaFactory;

    private InfrastructureAreaFactory $infrastructureAreaFactory;

    public function __construct(
        DomainAreaFactory $domainAreaFactory,
        InfrastructureAreaFactory $infrastructureAreaFactory,
        AreaInterface $parent,
        string $name
    ) {
        parent::__construct($parent, $name);

        $this->domainAreaFactory = $domainAreaFactory;
        $this->infrastructureAreaFactory = $infrastructureAreaFactory;
    }

    public function domain(): DomainAreaInterface
    {
        return $this->createSubArea('Domain', $this->domainAreaFactory->create(...));
    }

    public function infrastructure(): InfrastructureAreaInterface
    {
        return $this->createSubArea('Infrastructure', $this->infrastructureAreaFactory->create(...));
    }

    // application
    // - get events
    // - get event handlers
    // - get commands
    // - get command handlers
    // - get queries
    // - get query handlers
}
