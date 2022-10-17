<?php

declare(strict_types=1);

namespace SoureCode\DomainDrivenDesign\Writer;

interface WriterInterface
{
    public function write(string $path, string $content): void;
}
