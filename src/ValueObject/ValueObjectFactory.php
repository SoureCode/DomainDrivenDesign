<?php

declare(strict_types=1);

namespace SoureCode\DomainDrivenDesign\ValueObject;

use SoureCode\DomainDrivenDesign\Area\AreaInterface;
use SoureCode\PhpObjectModel\Model\ClassMethodModel;
use SoureCode\PhpObjectModel\Model\ClassModel;
use SoureCode\PhpObjectModel\Model\DeclareModel;
use SoureCode\PhpObjectModel\Model\PropertyModel;
use SoureCode\PhpObjectModel\Type\StringType;

class ValueObjectFactory implements ValueObjectFactoryInterface
{
    /**
     * @var class-string<ValueObject>
     */
    private string $className;

    /**
     * @param class-string<ValueObject> $className
     */
    public function __construct(string $className)
    {
        $this->className = $className;
    }

    public function create(AreaInterface $area, string $name): ValueObject
    {
        $valueObject = new ($this->className)($area, $name);

        $file = $valueObject->getClassFile();

        $file->setDeclare((new DeclareModel())->setStrictTypes(true))
            ->setNamespace($valueObject->getNamespace());

        $class = (new ClassModel($name))
            ->setFinal(true);

        $file->setClass($class);

        $constructor = new ClassMethodModel('__construct');
        $constructor->setPublic();

        $property = (new PropertyModel('value'))
            ->setReadonly(true)
            ->setPublic();

        $class
            ->addProperty($property)
            ->addMethod($constructor);

        $valueObject->setType(new StringType());

        return $valueObject;
    }
}
