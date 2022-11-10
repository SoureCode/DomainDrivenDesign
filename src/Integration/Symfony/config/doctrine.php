<?php

declare(strict_types=1);

use SoureCode\DomainDrivenDesign\Integration\Doctrine\DoctrineHelper;
use SoureCode\DomainDrivenDesign\Integration\Doctrine\Model\DoctrineModelFactory;
use SoureCode\DomainDrivenDesign\Integration\Doctrine\ValueObject\DoctrineValueObjectFactory;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container): void {
    require_once __DIR__ . '/_service_name_generator.php';

    $services = $container->services();

    $services->set(doctrineName('doctrine_helper'), DoctrineHelper::class)
        ->args([
            service('doctrine'),
        ]);

    $services->set(doctrineName('doctrine_value_object_factory'), DoctrineValueObjectFactory::class)
        ->decorate(domainName('value_object_factory'))
        ->args([
            service('.inner'),
            service(doctrineName('doctrine_helper')),
        ]);

    $services->set(doctrineName('doctrine_model_factory'), DoctrineModelFactory::class)
        ->decorate(domainName('model_factory'))
        ->args([
            service('.inner'),
            service(doctrineName('doctrine_helper')),
        ]);
};
