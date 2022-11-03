<?php

declare(strict_types=1);

namespace SoureCode\DomainDrivenDesign\Integration\Symfony;

use SoureCode\DomainDrivenDesign\Integration\Doctrine\ValueObject\DoctrineValueObject;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class SoureCodeDomainDrivenDesignBundle extends AbstractBundle
{
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $parameters = $container->parameters();

        $parameters->set('soure_code.domain_driven_design.value_object_class', DoctrineValueObject::class);
        $parameters->set('soure_code.domain_driven_design.directory', '%kernel.project_dir%/src');
        $parameters->set('soure_code.domain_driven_design.namespace', 'App');

        $container->import(__DIR__ . '/config/services.php');
    }
}
