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
        /**
         * @var ValueObject $valueObject
         */
        $valueObject = new ($this->className)($area, $name);

        $file = $valueObject->getClassFile();

        if (!$file->hasDeclare()) {
            $file->setDeclare((new DeclareModel())->setStrictTypes(true));
        }

        if (!$file->hasNamespace()) {
            $file->setNamespace($valueObject->getNamespace());
        }

        if (!$file->hasClass()) {
            $class = (new ClassModel($name))
                ->setFinal(true);

            $file->setClass($class);
        }

        $class = $file->getClass();

        if (!$class->hasMethod('__construct')) {
            $constructor = new ClassMethodModel('__construct');
            $constructor->setPublic();

            $class->addMethod($constructor);
        }

        if(!$class->hasProperty('value')) {
            $property = (new PropertyModel('value'))
                ->setReadonly(true)
                ->setPublic();

            $class->addProperty($property);
        }

        return $valueObject;
    }
}
