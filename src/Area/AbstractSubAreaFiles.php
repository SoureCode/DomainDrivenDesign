<?php

declare(strict_types=1);

namespace SoureCode\DomainDrivenDesign\Area;

use SoureCode\DomainDrivenDesign\Writer\WriterInterface;

abstract class AbstractSubAreaFiles extends AbstractSubArea
{
    use AreaFilesTrait;

    public function write(WriterInterface $writer): self
    {
        return $this->writeSubAreas($writer)
            ->writeFiles($writer);
    }
}
