<?php

declare(strict_types=1);

namespace SoureCode\DomainDrivenDesign\Area;

use SoureCode\DomainDrivenDesign\Writer\WriterInterface;
use SoureCode\PhpObjectModel\File\ClassFile;
use SoureCode\PhpObjectModel\Model\ClassModel;
use SoureCode\PhpObjectModel\Node\NodeFinder;
use SoureCode\PhpObjectModel\ValueObject\NamespaceName;
use Symfony\Component\Filesystem\Path;

abstract class AbstractAreaFile implements AreaFileInterface
{
    private readonly AreaInterface $area;

    private readonly string $name;

    private ?ClassFile $classFile = null;

    protected NodeFinder $finder;

    public function __construct(AreaInterface $area, string $name)
    {
        $this->area = $area;
        $this->name = $name;
        $this->finder = new NodeFinder();
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

    public function getClass(): ClassModel
    {
        return $this->getClassFile()->getClass();
    }

    public function getFile(): string
    {
        return Path::join($this->getDirectory(), $this->getName() . '.php');
    }

    public function getClassFile(): ClassFile
    {
        if (null === $this->classFile) {
            if (file_exists($this->getFile())) {
                $this->classFile = new ClassFile(file_get_contents($this->getFile()));
            } else {
                $this->classFile = new ClassFile();
            }
        }

        return $this->classFile;
    }

    public function write(WriterInterface $writer): self
    {
        if (null !== $this->classFile) {
            $writer->write($this->getFile(), $this->classFile->getSourceCode());
        }

        return $this;
    }
}
