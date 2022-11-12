<?php

declare(strict_types=1);

namespace SoureCode\DomainDrivenDesign\BoundingContext\Infrastructure\Repository;

enum RepositoryType: string
{
    case IN_MEMORY = 'in_memory';
    case DOCTRINE = 'doctrine';
}
