<?php

declare(strict_types=1);

namespace SoureCode\DomainDrivenDesign\Area;

use SoureCode\DomainDrivenDesign\Writer\WriterInterface;
use SoureCode\PhpObjectModel\File\AbstractFile;
use SoureCode\PhpObjectModel\File\ClassFile;
use SoureCode\PhpObjectModel\File\InterfaceFile;
use SoureCode\PhpObjectModel\Model\ClassModel;
use SoureCode\PhpObjectModel\Model\InterfaceModel;
use SoureCode\PhpObjectModel\Node\NodeFinder;
use SoureCode\PhpObjectModel\ValueObject\NamespaceName;
use Symfony\Component\Filesystem\Path;

/**
 * @template-covariant T of AbstractFile
 *
 * @implements AreaFileInterface<T>
 */
abstract class AbstractAreaFile implements AreaFileInterface
{
    private readonly AreaInterface $area;

    private readonly string $name;

    /**
     * @psalm-var T
     */
    private ?AbstractFile $file = null;

    protected NodeFinder $finder;

    public function __construct(AreaInterface $area, string $name)
    {
        $this->area = $area;
        $this->name = $name;
        $this->finder = new NodeFinder();
    }

    /**
     * @psalm-return class-string<T>
     */
    public function getFileTypeClassName(): string
    {
        return ClassFile::class;
    }

    public function getNamespace(): NamespaceName
    {
        return $this->area->getNamespace();
    }

    public function getDirectory(): string
    {
        return $this->area->getDirectory();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getClassName(): string
    {
        return $this->name;
    }

    public function getClass(): ClassModel
    {
        $file = $this->getFile();

        if (!$file instanceof ClassFile) {
            throw new \RuntimeException('File is not a class file.');
        }

        return $file->getClass();
    }

    public function getInterface(): InterfaceModel
    {
        $file = $this->getFile();

        if (!$file instanceof InterfaceFile) {
            throw new \RuntimeException('File is not a interface file.');
        }

        return $file->getInterface();
    }

    public function getFilePath(): string
    {
        return Path::join($this->getDirectory(), $this->getClassName() . '.php');
    }

    /**
     * @psalm-return T
     */
    public function getFile(): AbstractFile
    {
        if (null === $this->file) {
            $filePath = $this->getFilePath();

            if (file_exists($filePath)) {
                $this->file = $this->openFile($filePath);
            } else {
                $this->file = $this->createFile();
            }
        }

        return $this->file;
    }

    public function write(WriterInterface $writer): self
    {
        if (null !== $this->file) {
            $writer->write($this->getFilePath(), $this->file->getSourceCode());
        }

        return $this;
    }

    /**
     * @psalm-return T
     */
    private function openFile(string $filePath): AbstractFile
    {
        $className = $this->getFileTypeClassName();

        return new ($className)(file_get_contents($filePath));
    }

    /**
     * @psalm-return T
     */
    private function createFile(): AbstractFile
    {
        $className = $this->getFileTypeClassName();

        return new ($className)();
    }
}
