<?php

declare(strict_types=1);

namespace SoureCode\DomainDrivenDesign\Tests\Integration\Symfony;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Nyholm\BundleTest\TestKernel;
use SoureCode\DomainDrivenDesign\Integration\Symfony\SoureCodeDomainDrivenDesignBundle;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpKernel\KernelInterface;

abstract class AbstractTestCase extends KernelTestCase
{
    protected static function getKernelClass(): string
    {
        return TestKernel::class;
    }

    protected static function createKernel(array $options = []): KernelInterface
    {
        /**
         * @var TestKernel $kernel
         */
        $kernel = parent::createKernel($options);
        $kernel->addTestBundle(DoctrineBundle::class);
        $kernel->addTestBundle(SoureCodeDomainDrivenDesignBundle::class);
        $kernel->addTestConfig(__DIR__ . '/config/doctrine.yaml');
        $kernel->setTestProjectDir(__DIR__);
        $kernel->handleOptions($options);

        return $kernel;
    }
}
