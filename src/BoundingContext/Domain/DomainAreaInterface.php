<?php

declare(strict_types=1);

namespace SoureCode\DomainDrivenDesign\BoundingContext\Domain;

use SoureCode\DomainDrivenDesign\BoundingContext\Domain\Model\ModelArea;
use SoureCode\DomainDrivenDesign\BoundingContext\Domain\Model\ModelAreaInterface;
use SoureCode\DomainDrivenDesign\BoundingContext\Domain\Repository\RepositoryArea;
use SoureCode\DomainDrivenDesign\BoundingContext\Domain\Repository\RepositoryAreaInterface;
use SoureCode\DomainDrivenDesign\BoundingContext\Domain\ValueObject\ValueObjectAreaInterface;

interface DomainAreaInterface extends ModelAreaInterface, RepositoryAreaInterface, ValueObjectAreaInterface
{
    public function model(): ModelArea;

    public function valueObject(): ValueObjectAreaInterface;

    public function repository(): RepositoryArea;
}
