<?php

declare(strict_types=1);

namespace SoureCode\DomainDrivenDesign\Model;

use SoureCode\DomainDrivenDesign\Area\AreaInterface;
use SoureCode\PhpObjectModel\Model\ClassModel;
use SoureCode\PhpObjectModel\Model\DeclareModel;

class ModelFactory implements ModelFactoryInterface
{
    public function create(AreaInterface $area, string $name): Model
    {
        $model = new Model($area, $name);

        $file = $model->getClassFile();

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
