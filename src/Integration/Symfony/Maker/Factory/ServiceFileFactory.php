<?php

declare(strict_types=1);

namespace SoureCode\DomainDrivenDesign\Integration\Symfony\Maker\Factory;

use PhpParser\Node;
use SoureCode\PhpObjectModel\File\ClosureFile;
use SoureCode\PhpObjectModel\Model\ClosureModel;
use SoureCode\PhpObjectModel\Model\DeclareModel;
use SoureCode\PhpObjectModel\Type\ClassType;
use SoureCode\PhpObjectModel\ValueObject\NamespaceName;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Filesystem\Path;

class ServiceFileFactory
{
    private readonly string $projectDirectory;

    public function __construct(string $projectDirectory)
    {
        $this->projectDirectory = $projectDirectory;
    }

    public function create(NamespaceName|string $namespace, string $directory, bool $load = true): ClosureFile
    {
        $namespace = is_string($namespace) ? new NamespaceName($namespace) : $namespace;

        $file = (new ClosureFile('<?php'))
            ->setDeclare((new DeclareModel())->setStrictTypes(true));

        $closure = new ClosureModel();

        $file->setClosure($closure);

        $closure->addParameter('containerConfigurator', new ClassType(ContainerConfigurator::class));

        // $services = $containerConfigurator->services();
        $closure->addStatement(
            new Node\Expr\Assign(
                new Node\Expr\Variable('services'),
                new Node\Expr\MethodCall(
                    new Node\Expr\Variable('containerConfigurator'),
                    'services'
                ),
            )
        );

        /*
         * $services->defaults()
         * ->autowire()
         * ->autoconfigure();
         */
        $closure->addStatement(
            new Node\Expr\MethodCall(
                new Node\Expr\MethodCall(
                    new Node\Expr\MethodCall(
                        new Node\Expr\Variable('services'),
                        'defaults',
                    ),
                    'autowire',
                ),
                'autoconfigure',
            )
        );

        if ($load) {
            /*
             * $services->load('App\\<...>\\', __DIR__.'/../../src/<...>');
             */
            $directory = Path::makeRelative($directory, Path::join($this->projectDirectory, 'config/services'));
            $closure->addStatement(
                new Node\Expr\MethodCall(
                    new Node\Expr\Variable('services'),
                    'load',
                    [
                        new Node\Scalar\String_($namespace->getName().'\\'),
                        new Node\Expr\BinaryOp\Concat(
                            new Node\Scalar\MagicConst\Dir(),
                            new Node\Scalar\String_('/'.$directory),
                        ),
                    ]
                )
            );
        }

        return $file;
    }
}
