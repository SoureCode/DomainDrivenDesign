<?php

declare(strict_types=1);

namespace SoureCode\DomainDrivenDesign\Writer;

use Symfony\Component\Filesystem\Filesystem;

class FilesystemWriter implements WriterInterface
{
    private Filesystem $filesystem;

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function write(string $path, string $content): void
    {
        $this->filesystem->dumpFile($path, $content);
    }
}
