<?php

declare(strict_types=1);

namespace SoureCode\DomainDrivenDesign\Model;

use SoureCode\DomainDrivenDesign\Area\AreaInterface;

interface ModelFactoryInterface
{
    public function create(AreaInterface $area, string $name): Model;
}
