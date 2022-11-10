<?php

declare(strict_types=1);

namespace SoureCode\DomainDrivenDesign\BoundingContext\Domain\Model;

use SoureCode\DomainDrivenDesign\Area\AreaInterface;
use SoureCode\PhpObjectModel\Model\ClassModel;
use SoureCode\PhpObjectModel\Model\DeclareModel;

class ModelFactory implements ModelFactoryInterface
{
    /**
     * @var class-string<ModelInterface>
     */
    private string $modelClass;

    /**
     * @param class-string<ModelInterface> $modelClass
     */
    public function __construct(string $modelClass)
    {
        $this->modelClass = $modelClass;
    }

    public function create(AreaInterface $area, string $name): ModelInterface
    {
        $model = new ($this->modelClass)($area, $name);

        $file = $model->getFile();

        if (!$file->hasDeclare()) {
            $file->setDeclare((new DeclareModel())->setStrictTypes(true));
        }

        if (!$file->hasNamespace()) {
            $file->setNamespace($model->getNamespace());
        }

        if (!$file->hasClass()) {
            $file->setClass(new ClassModel($name));
        }

        return $model;
    }
}
