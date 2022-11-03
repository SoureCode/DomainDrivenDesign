<?php

declare(strict_types=1);

namespace SoureCode\DomainDrivenDesign\BoundingContext;

use SoureCode\DomainDrivenDesign\Area\AbstractSubArea;
use SoureCode\DomainDrivenDesign\Area\AreaInterface;
use SoureCode\DomainDrivenDesign\Domain\DomainArea;
use SoureCode\DomainDrivenDesign\Domain\DomainAreaFactory;

class BoundingContextArea extends AbstractSubArea
{
    private DomainAreaFactory $domainAreaFactory;

    public function __construct(DomainAreaFactory $domainAreaFactory, AreaInterface $parent, string $name)
    {
        parent::__construct($parent, $name);

        $this->domainAreaFactory = $domainAreaFactory;
    }

    public function domain(): DomainArea
    {
        return $this->createSubArea('Domain', $this->domainAreaFactory->create(...));
    }

    // getApplicationArea
    // - get events
    // - get event handlers
    // - get commands
    // - get command handlers
    // - get queries
    // - get query handlers
    // getInfrastructureArea
}
