<?php

declare(strict_types=1);

namespace SoureCode\DomainDrivenDesign\Factory;

use SoureCode\DomainDrivenDesign\Area\AreaInterface;
use SoureCode\DomainDrivenDesign\Doctrine\DoctrineHelper;
use SoureCode\DomainDrivenDesign\Files\ValueObject;

class ValueObjectFactory
{
    private DoctrineHelper $doctrineHelper;

    public function __construct(DoctrineHelper $doctrineHelper)
    {
        $this->doctrineHelper = $doctrineHelper;
    }

    public function create(AreaInterface $area, string $name): ValueObject
    {
        return new ValueObject($this->doctrineHelper, $area, $name);
    }
}
