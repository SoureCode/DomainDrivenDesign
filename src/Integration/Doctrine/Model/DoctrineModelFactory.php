<?php

declare(strict_types=1);

namespace SoureCode\DomainDrivenDesign\Integration\Doctrine\Model;

use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;
use SoureCode\DomainDrivenDesign\Area\AreaInterface;
use SoureCode\DomainDrivenDesign\Integration\Doctrine\DoctrineHelper;
use SoureCode\DomainDrivenDesign\Model\Model;
use SoureCode\DomainDrivenDesign\Model\ModelFactoryInterface;
use SoureCode\PhpObjectModel\Model\ArgumentModel;
use SoureCode\PhpObjectModel\Model\AttributeModel;
use SoureCode\PhpObjectModel\Value\StringValue;

class DoctrineModelFactory implements ModelFactoryInterface
{
    protected ModelFactoryInterface $modelFactory;

    protected DoctrineHelper $doctrineHelper;

    public function __construct(ModelFactoryInterface $modelFactory, DoctrineHelper $doctrineHelper)
    {
        $this->modelFactory = $modelFactory;
        $this->doctrineHelper = $doctrineHelper;
    }

    public function create(AreaInterface $area, string $name): Model
    {
        $model = $this->modelFactory->create($area, $name);

        $file = $model->getClassFile();

        $file->addUse('Doctrine\\ORM\\Mapping', 'ORM');

        $class = $file->getClass();
        $class->addAttribute(Entity::class);

        if ($this->doctrineHelper->isKeyword($name)) {
            $tableAttribute = new AttributeModel(Table::class);

            $tableName = $this->doctrineHelper->getPotentialTableName($name);
            $escapedTableName = $this->doctrineHelper->escapeName($tableName);

            $tableAttribute->setArgument(
                new ArgumentModel(
                    'name',
                    new StringValue($escapedTableName)
                )
            );

            $class->addAttribute($tableAttribute);
        }

        return $model;
    }
}
