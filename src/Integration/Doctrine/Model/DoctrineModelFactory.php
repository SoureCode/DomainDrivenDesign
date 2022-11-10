<?php

declare(strict_types=1);

namespace SoureCode\DomainDrivenDesign\Integration\Doctrine\Model;

use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;
use SoureCode\DomainDrivenDesign\Area\AreaInterface;
use SoureCode\DomainDrivenDesign\BoundingContext\Domain\Model\ModelFactoryInterface;
use SoureCode\DomainDrivenDesign\BoundingContext\Domain\Model\ModelInterface;
use SoureCode\DomainDrivenDesign\Integration\Doctrine\DoctrineHelper;
use SoureCode\PhpObjectModel\Model\ArgumentModel;
use SoureCode\PhpObjectModel\Model\AttributeModel;
use SoureCode\PhpObjectModel\Model\UseModel;
use SoureCode\PhpObjectModel\Value\StringValue;

class DoctrineModelFactory implements \SoureCode\DomainDrivenDesign\BoundingContext\Domain\Model\ModelFactoryInterface
{
    protected \SoureCode\DomainDrivenDesign\BoundingContext\Domain\Model\ModelFactoryInterface $modelFactory;

    protected DoctrineHelper $doctrineHelper;

    public function __construct(ModelFactoryInterface $modelFactory, DoctrineHelper $doctrineHelper)
    {
        $this->modelFactory = $modelFactory;
        $this->doctrineHelper = $doctrineHelper;
    }

    public function create(AreaInterface $area, string $name): ModelInterface
    {
        $model = $this->modelFactory->create($area, $name);

        $file = $model->getFile();

        $useModel = new UseModel('Doctrine\\ORM\\Mapping', 'ORM');

        if (!$file->hasUse($useModel)) {
            $file->addUse($useModel);
        }

        $class = $file->getClass();

        if (!$class->hasAttribute(Entity::class)) {
            $class->addAttribute(Entity::class);
        }

        if (!$class->hasAttribute(Table::class)) {
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
        }

        return $model;
    }
}
