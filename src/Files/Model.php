<?php

declare(strict_types=1);

namespace SoureCode\DomainDrivenDesign\Files;

use Doctrine\ORM\Mapping\Embedded;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;
use RuntimeException;
use SoureCode\DomainDrivenDesign\Area\AbstractAreaFile;
use SoureCode\DomainDrivenDesign\Area\AreaInterface;
use SoureCode\DomainDrivenDesign\Doctrine\DoctrineHelper;
use SoureCode\PhpObjectModel\Model\ArgumentModel;
use SoureCode\PhpObjectModel\Model\AttributeModel;
use SoureCode\PhpObjectModel\Model\ClassModel;
use SoureCode\PhpObjectModel\Model\DeclareModel;
use SoureCode\PhpObjectModel\Model\PropertyModel;
use SoureCode\PhpObjectModel\Type\ClassType;
use SoureCode\PhpObjectModel\Value\BooleanValue;
use SoureCode\PhpObjectModel\Value\StringValue;

final class Model extends AbstractAreaFile
{
    public function __construct(DoctrineHelper $doctrineHelper, AreaInterface $area, string $name)
    {
        parent::__construct($area, $name);

        $file = $this->getClassFile();

        $file
            ->setDeclare((new DeclareModel())->setStrictTypes(true))
            ->setNamespace($this->getNamespace())
            ->addUse('Doctrine\\ORM\\Mapping', 'ORM');

        $class = (new ClassModel($name))
            ->addAttribute(Entity::class);

        $file->setClass($class);

        $tableName = $doctrineHelper->getPotentialTableName($name);

        if ($doctrineHelper->isKeyword($name)) {
            $class->addAttribute(
                (new AttributeModel(Table::class))
                    ->setArgument(
                        new ArgumentModel(
                            'name',
                            new StringValue('"' . $tableName . '"')
                        )
                    )
            );
        }
    }

    public function addProperty(string $name, ValueObject $valueObject): PropertyModel
    {
        $classFile = $this->getClassFile();
        $class = $classFile->getClass();

        if ($class->hasProperty($name)) {
            throw new RuntimeException(sprintf('Property "%s" already exists.', $name));
        }

        $property = new PropertyModel(
            $name,
            new ClassType(
                $valueObject->getNamespace()
                    ->class($valueObject->getName())
            )
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

        return $property;
    }
}
