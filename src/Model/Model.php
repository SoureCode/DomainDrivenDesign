<?php

declare(strict_types=1);

namespace SoureCode\DomainDrivenDesign\Model;

use Doctrine\ORM\Mapping\Embedded;
use PhpParser\Node;
use RuntimeException;
use SoureCode\DomainDrivenDesign\Area\AbstractAreaFile;
use SoureCode\DomainDrivenDesign\ValueObject\ValueObject;
use SoureCode\PhpObjectModel\Model\ArgumentModel;
use SoureCode\PhpObjectModel\Model\AttributeModel;
use SoureCode\PhpObjectModel\Model\ParameterModel;
use SoureCode\PhpObjectModel\Model\PropertyModel;
use SoureCode\PhpObjectModel\Type\ClassType;
use SoureCode\PhpObjectModel\Value\BooleanValue;
use Symfony\Component\String\UnicodeString;

final class Model extends AbstractAreaFile
{
    public function addProperty(ValueObject $valueObject, string $name = null, bool $assign = true): PropertyModel
    {
        $class = $this->getClass();
        $className = $valueObject
            ->getNamespace()
            ->class($valueObject->getName());

        $name = (new UnicodeString($name ?? $valueObject->getName()))
            ->camel()->toString();

        if ($class->hasProperty($name)) {
            throw new RuntimeException(sprintf('Property "%s" already exists.', $name));
        }

        $property = new PropertyModel(
            $name,
            new ClassType($className)
        );

        $class->addProperty($property);

        $property
            ->setPublic()
            ->addAttribute(
                (new AttributeModel(Embedded::class))
                    ->setArgument(
                        new ArgumentModel(
                            'columnPrefix',
                            new BooleanValue(false)
                        )
                    )
            );

        if (!$class->hasMethod('__construct')) {
            $class->addMethod('__construct');
        }

        $constructor = $class->getMethod('__construct');
        $constructor->setPublic();

        if ($assign) {
            $constructorParameter = new ParameterModel(
                $name,
                new ClassType($className),
            );

            $constructor->addParameter($constructorParameter);

            $constructor->addStatement(
                new Node\Expr\Assign(
                    new Node\Expr\PropertyFetch(
                        new Node\Expr\Variable('this'),
                        $name
                    ),
                    new Node\Expr\Variable($name)
                )
            );
        } else {
            $constructor->addStatement(
                new Node\Expr\Assign(
                    new Node\Expr\PropertyFetch(
                        new Node\Expr\Variable('this'),
                        $name
                    ),
                    new Node\Expr\New_(
                        new Node\Name($className->getShortName())
                    )
                )
            );
        }

        return $property;
    }
}
