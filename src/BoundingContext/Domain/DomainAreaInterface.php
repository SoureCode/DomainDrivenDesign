<?php

declare(strict_types=1);

namespace SoureCode\DomainDrivenDesign\BoundingContext\Domain;

use SoureCode\DomainDrivenDesign\BoundingContext\Domain\Model\ModelAreaInterface;
use SoureCode\DomainDrivenDesign\BoundingContext\Domain\Repository\RepositoryInterfaceAreaInterface;
use SoureCode\DomainDrivenDesign\BoundingContext\Domain\ValueObject\ValueObjectAreaInterface;

interface DomainAreaInterface extends ModelAreaInterface, RepositoryInterfaceAreaInterface, ValueObjectAreaInterface
{
    public function model(): ModelAreaInterface;

    public function valueObject(): ValueObjectAreaInterface;

    public function repository(): RepositoryInterfaceAreaInterface;
}
