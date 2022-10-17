<?php

declare(strict_types=1);

namespace SoureCode\DomainDrivenDesign\Area;

interface SubAreaInterface extends AreaInterface
{
    public function getName(): string;
}
