<?php

declare(strict_types=1);

namespace SoureCode\DomainDrivenDesign\Writer;

class MemoryWriter implements WriterInterface
{
    /**
     * @var array<string, string>
     */
    private array $files = [];

    private ?string $base = null;

    public function __construct(?string $base = null)
    {
        $this->base = $base;
    }

    public function write(string $path, string $content): void
    {
        if (null !== $this->base && str_starts_with($path, $this->base)) {
            $path = substr($path, strlen($this->base));
        }

        $this->files[$path] = $content;
    }

    public function getFiles(): array
    {
        return $this->files;
    }

    public function getFile(string $path): string
    {
        return $this->files[$path];
    }

    public function hasFile(string $path): bool
    {
        return isset($this->files[$path]);
    }

    public function clear(): void
    {
        $this->files = [];
    }
}
