<?php

declare(strict_types=1);

use SoureCode\DomainDrivenDesign\BoundingContext\BoundingContextAreaFactory;
use SoureCode\DomainDrivenDesign\Domain\DomainAreaFactory;
use SoureCode\DomainDrivenDesign\DomainDrivenDesign;
use SoureCode\DomainDrivenDesign\Integration\Doctrine\DoctrineHelper;
use SoureCode\DomainDrivenDesign\Integration\Doctrine\Model\DoctrineModelFactory;
use SoureCode\DomainDrivenDesign\Integration\Doctrine\ValueObject\DoctrineValueObjectFactory;
use SoureCode\DomainDrivenDesign\Model\ModelAreaFactory;
use SoureCode\DomainDrivenDesign\Model\ModelFactory;
use SoureCode\DomainDrivenDesign\ValueObject\ValueObjectAreaFactory;
use SoureCode\DomainDrivenDesign\ValueObject\ValueObjectFactory;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\param;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $provider = 'soure_code';
    $package = 'domain_driven_design';
    $prefix = $provider . '.' . $package;

    $name = static fn (string $name): string => $prefix . '.' . $name;

    $services->set($name('value_object_factory'), ValueObjectFactory::class)
        ->args([
            param($name('value_object_class')),
        ]);

    $services->set($name('value_object_area_factory'), ValueObjectAreaFactory::class)
        ->args([
            service($name('value_object_factory')),
        ]);

    $services->set($name('model_factory'), ModelFactory::class);

    $services->set($name('model_area_factory'), ModelAreaFactory::class)
        ->args([
            service($name('model_factory')),
        ]);

    $services->set($name('domain_area_factory'), DomainAreaFactory::class)
        ->args([
            service($name('model_area_factory')),
            service($name('value_object_area_factory')),
        ]);

    $services->set($name('bounding_context_area_factory'), BoundingContextAreaFactory::class)
        ->args([
            service($name('domain_area_factory')),
        ]);

    $services->set($name('domain_driven_design'), DomainDrivenDesign::class)
        ->public()
        ->args([
            service($name('bounding_context_area_factory')),
            param($name('directory')),
            param($name('namespace')),
        ]);

    // Doctrine integration
    // @todo enable this only if doctrine is installed
    $services->set($name('doctrine_helper'), DoctrineHelper::class)
        ->args([
            service('doctrine'),
        ]);

    $services->set($name('doctrine_value_object_factory'), DoctrineValueObjectFactory::class)
        ->decorate($name('value_object_factory'))
        ->args([
            service($name('value_object_factory')),
            service($name('doctrine_helper')),
        ]);

    $services->set($name('doctrine_model_factory'), DoctrineModelFactory::class)
        ->decorate($name('model_factory'))
        ->args([
            service($name('model_factory')),
            service($name('doctrine_helper')),
        ]);
};
