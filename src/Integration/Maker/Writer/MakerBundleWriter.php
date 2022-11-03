<?php

declare(strict_types=1);

namespace SoureCode\DomainDrivenDesign\Integration\Maker\Writer;

use SoureCode\DomainDrivenDesign\Writer\WriterInterface;
use Symfony\Bundle\MakerBundle\Generator;

class MakerBundleWriter implements WriterInterface
{
    private Generator $generator;

    public function __construct(Generator $generator)
    {
        $this->generator = $generator;
    }

    public function write(string $path, string $content): void
    {
        $this->generator->dumpFile($path, $content);
    }
}
