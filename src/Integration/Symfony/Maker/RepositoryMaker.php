<?php

declare(strict_types=1);

namespace SoureCode\DomainDrivenDesign\Integration\Symfony\Maker;

use Doctrine\ORM\EntityManagerInterface;
use PhpParser\Comment;
use PhpParser\Node;
use SoureCode\DomainDrivenDesign\BoundingContext\BoundingContextAreaInterface;
use SoureCode\DomainDrivenDesign\BoundingContext\Domain\DomainAreaInterface;
use SoureCode\DomainDrivenDesign\BoundingContext\Domain\Model\ModelInterface;
use SoureCode\DomainDrivenDesign\BoundingContext\Domain\Repository\RepositoryInterfaceInterface;
use SoureCode\DomainDrivenDesign\BoundingContext\Infrastructure;
use SoureCode\DomainDrivenDesign\BoundingContext\Infrastructure\InfrastructureAreaInterface;
use SoureCode\DomainDrivenDesign\BoundingContext\Infrastructure\Repository\RepositoryInterface;
use SoureCode\DomainDrivenDesign\BoundingContext\Infrastructure\Repository\RepositoryType;
use SoureCode\DomainDrivenDesign\DomainDrivenDesign;
use SoureCode\DomainDrivenDesign\Integration\Maker\Writer\MakerBundleWriter;
use SoureCode\DomainDrivenDesign\Integration\Symfony\File\ServicesClosureFile;
use SoureCode\PhpObjectModel\Model\ClassConstModel;
use SoureCode\PhpObjectModel\Model\ClassMethodModel;
use SoureCode\PhpObjectModel\Model\ParameterModel;
use SoureCode\PhpObjectModel\Type\ClassType;
use SoureCode\PhpObjectModel\Value\ClassConstValue;
use SoureCode\PhpObjectModel\Value\StringValue;
use SoureCode\PhpObjectModel\ValueObject\ClassName;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\Util\YamlSourceManipulator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

class RepositoryMaker extends AbstractDomainDrivenDesignMaker
{
    /**
     * @var array<string, ServicesClosureFile>
     */
    private array $serviceConfigFiles = [];

    private ClassName $repositoryInterfaceClass;

    private ClassName $doctrineRepositoryClass;

    private ClassName $inMemoryRepositoryClass;

    /**
     * @psalm-param class-string $repositoryInterfaceClass
     */
    public function __construct(
        DomainDrivenDesign $domainDrivenDesign,
        string $repositoryInterfaceClass,
        string $doctrineRepositoryClass,
        string $inMemoryRepositoryClass,
    ) {
        parent::__construct($domainDrivenDesign);

        $this->repositoryInterfaceClass = ClassName::fromString($repositoryInterfaceClass);
        $this->doctrineRepositoryClass = ClassName::fromString($doctrineRepositoryClass);
        $this->inMemoryRepositoryClass = ClassName::fromString($inMemoryRepositoryClass);
    }

    public static function getCommandName(): string
    {
        return 'make:ddd:repository';
    }

    public static function getCommandDescription(): string
    {
        return 'Creates a new DDD repository';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConfig): void
    {
        $command
            ->addArgument(
                'bounding-context',
                InputArgument::REQUIRED,
                'The name of the bounding context (e.g. <fg=yellow>BookStore</>)',
                null,
                $this->getBoundingContextNames(...),
            )
            ->addArgument(
                'model',
                InputArgument::REQUIRED,
                'The name of the model to create (e.g. <fg=yellow>Book</>)',
                null,
                $this->resolveModelNames(...),
            )
            ->addArgument(
                'type',
                InputArgument::REQUIRED,
                'The type of the repository (e.g. <fg=yellow>in_memory</> or <fg=yellow>doctrine</>)',
                null,
                $this->getRepositoryTypes(...),
            )
            ->addOption(
                'service',
                null,
                InputOption::VALUE_NONE,
                'If set, the repository will be registered as the default service',
            )
            ->addOption(
                'test',
                null,
                InputOption::VALUE_NONE,
                'If set, the repository will be registered as the default test service',
            );

        $inputConfig->setArgumentAsNonInteractive('bounding-context');
        $inputConfig->setArgumentAsNonInteractive('model');
        $inputConfig->setArgumentAsNonInteractive('type');
    }

    public function interact(InputInterface $input, ConsoleStyle $io, Command $command): void
    {
        $this->askArgumentChoice($input, $io, $command, 'bounding-context', $this->getBoundingContextChoices(...));
        $this->askArgumentChoice(
            $input, $io, $command, 'model',
            fn () => $this->getModelChoices($input->getArgument('bounding-context'))
        );
        $this->askArgumentChoice($input, $io, $command, 'type', $this->getRepositoryTypeChoices(...));
    }

    public function configureDependencies(DependencyBuilder $dependencies): void
    {
    }

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator): void
    {
        $modelName = $input->getArgument('model');
        $boundingContextName = $input->getArgument('bounding-context');
        $type = $input->getArgument('type');
        $repositoryType = Infrastructure\Repository\RepositoryType::from($type);

        $boundingContext = $this->domainDrivenDesign->getBoundingContext($boundingContextName);

        $domain = $boundingContext->domain();
        $infrastructure = $boundingContext->infrastructure();

        $model = $domain->getModel($modelName);
        $repository = $this->resolveRepositoryInterface($domain, $model);
        $repositoryImplementation = $this->resolveRepositoryImplementation(
            $infrastructure,
            $model,
            $repositoryType,
            $repository
        );

        if ($input->hasOption('test') && $input->getOption('test')) {
            $this->ensureRegisteredAsTestService($generator, $boundingContext, $repository, $repositoryImplementation);
        }

        if ($input->hasOption('service') && $input->getOption('service')) {
            $this->ensureRegisteredAsService($generator, $boundingContext, $repository, $repositoryImplementation);
        }

        $this->ensurePublicInTestService($generator, $boundingContext, $repositoryImplementation);

        if (RepositoryType::DOCTRINE == $repositoryType) {
            $this->manipulateDoctrineConfig($generator, $boundingContext, $model);
        }

        $this->domainDrivenDesign->write(new MakerBundleWriter($generator));

        $generator->writeChanges();

        $this->writeSuccessMessage($io);
        $io->text([
            'Next: create some commands!',
        ]);
    }

    protected function getTestServiceConfig(BoundingContextAreaInterface $boundingContext): ServicesClosureFile
    {
        $serviceConfigFilePath = $this->getTestServiceConfigFilePath($boundingContext);

        if (!array_key_exists($serviceConfigFilePath, $this->serviceConfigFiles)) {
            $servicesSourceCode = file_get_contents($serviceConfigFilePath);

            $this->serviceConfigFiles[$serviceConfigFilePath] = new ServicesClosureFile($servicesSourceCode);
        }

        return $this->serviceConfigFiles[$serviceConfigFilePath];
    }

    protected function getServiceConfig(BoundingContextAreaInterface $boundingContext): ServicesClosureFile
    {
        $serviceConfigFilePath = $this->getServiceConfigFilePath($boundingContext);

        if (!array_key_exists($serviceConfigFilePath, $this->serviceConfigFiles)) {
            $contents = file_get_contents($serviceConfigFilePath);

            $this->serviceConfigFiles[$serviceConfigFilePath] = new ServicesClosureFile($contents);
        }

        return $this->serviceConfigFiles[$serviceConfigFilePath];
    }

    protected function getTestServiceConfigFilePath(BoundingContextAreaInterface $boundingContext): string
    {
        $slugName = $this->getSlugName($boundingContext->getName());

        return 'config/services/test/' . $slugName . '.php';
    }

    protected function getServiceConfigFilePath(BoundingContextAreaInterface $boundingContext): string
    {
        $slugName = $this->getSlugName($boundingContext->getName());

        return 'config/services/' . $slugName . '.php';
    }

    private function ensurePublicInTestService(Generator $generator, BoundingContextAreaInterface $boundingContext, RepositoryInterface $repositoryImplementation): void
    {
        $serviceConfigFilePath = $this->getTestServiceConfigFilePath($boundingContext);
        $file = $this->getTestServiceConfig($boundingContext);

        if (!$file->hasServiceSet($repositoryImplementation->getClass()->getName())) {
            $file->setServiceCall(
                $repositoryImplementation->getClass()->getName()
            )
                ->public();
        }

        $generator->dumpFile($serviceConfigFilePath, $file->getSourceCode());
    }

    private function ensureRegisteredAsTestService(Generator $generator, BoundingContextAreaInterface $boundingContext, RepositoryInterfaceInterface $repositoryInterface, RepositoryInterface $repositoryImplementation): void
    {
        $serviceConfigFilePath = $this->getTestServiceConfigFilePath($boundingContext);
        $file = $this->getTestServiceConfig($boundingContext);
        $id = $repositoryInterface->getInterface()->getName();
        $implementationId = $repositoryImplementation->getClass()->getName();

        if (!$file->hasServiceSet($id)) {
            $file->setServiceCall($id, $implementationId);
        } else {
            $file->getServiceSet($id)
                ->class($implementationId);
        }

        $generator->dumpFile($serviceConfigFilePath, $file->getSourceCode());
    }

    private function ensureRegisteredAsService(Generator $generator, BoundingContextAreaInterface $boundingContext, RepositoryInterfaceInterface $repositoryInterface, RepositoryInterface $repositoryImplementation): void
    {
        $serviceConfigFilePath = $this->getServiceConfigFilePath($boundingContext);
        $file = $this->getServiceConfig($boundingContext);
        $id = $repositoryInterface->getInterface()->getName();
        $implementationId = $repositoryImplementation->getClass()->getName();

        if (!$file->hasServiceSet($id)) {
            $file->setServiceCall($id, $implementationId);
        } else {
            $file->getServiceSet($id)
                ->class($implementationId);
        }

        $generator->dumpFile($serviceConfigFilePath, $file->getSourceCode());
    }

    private function resolveRepositoryInterface(DomainAreaInterface $domain, ModelInterface $model): RepositoryInterfaceInterface
    {
        $modelFile = $model->getFile();
        $modelName = $model->getName();
        $modelClassName = $modelFile->getNamespace()->getName()->class($modelName);
        $repositoryFile = $domain->createRepository($modelName);
        $repositoryClassFile = $repositoryFile->getFile();
        $repository = $repositoryFile->getInterface();

        if (!$repository->extends($this->repositoryInterfaceClass)) {
            $repository->extend($this->repositoryInterfaceClass);
        }

        if (!$repositoryClassFile->hasUse($modelClassName)) {
            $repositoryClassFile->addUse($modelClassName);
        }

        $repositoryInterfaceName = $this->repositoryInterfaceClass->getShortName();
        $node = $repository->getNode();
        $node->setDocComment(
            new Comment\Doc(
                <<<HEREDOC
/**
 * @extends $repositoryInterfaceName<$modelName>
 */
HEREDOC
            )
        );

        return $repositoryFile;
    }

    private function resolveRepositoryImplementation(
        InfrastructureAreaInterface $infrastructure,
        ModelInterface $model,
        RepositoryType $type,
        RepositoryInterfaceInterface $baseRepository
    ): RepositoryInterface {
        $modelFile = $model->getFile();
        $modelName = $model->getName();
        $modelClassName = $modelFile->getNamespace()->getName()->class($modelName);
        $repository = $infrastructure->createRepository($modelName, $type);
        $repositoryClassFile = $repository->getFile();
        $class = $repository->getClass();

        if (!$repositoryClassFile->hasUse($modelClassName)) {
            $repositoryClassFile->addUse($modelClassName);
        }

        if (RepositoryType::IN_MEMORY === $type) {
            $class
                ->setFinal(true)
                ->extend($this->inMemoryRepositoryClass)
                ->implement($baseRepository->getInterface());

            $inMemoryRepositoryClassName = $this->inMemoryRepositoryClass->getShortName();

            $node = $class->getNode();
            $node->setDocComment(
                new Comment\Doc(
                    <<<HEREDOC
/**
 * @extends $inMemoryRepositoryClassName<$modelName>
 */
HEREDOC
                )
            );
        } elseif (RepositoryType::DOCTRINE === $type) {
            $class
                ->setFinal(true)
                ->extend($this->doctrineRepositoryClass)
                ->implement($baseRepository->getInterface());

            $doctrineRepositoryClassName = $this->doctrineRepositoryClass->getShortName();

            /**
             * @var Node\Stmt\Class_ $node
             */
            $node = $class->getNode();
            $node->setDocComment(
                new Comment\Doc(
                    <<<HEREDOC
/**
 * @extends $doctrineRepositoryClassName<$modelName>
 */
HEREDOC
                )
            );

            /*
             * private const ENTITY_CLASS = User::class;
             * private const ALIAS = 'user';
             **/
            $aliasClassConst = new ClassConstModel('ALIAS', new StringValue($this->getSlugName($modelName)));
            $aliasClassConst->setPrivate();

            $entityClassConst = new ClassConstModel('ENTITY_CLASS', new ClassConstValue($modelClassName, 'class'));
            $entityClassConst->setPrivate();

            if (!$class->hasConstant($aliasClassConst)) {
                $class->addConstant($aliasClassConst);
            }

            if (!$class->hasConstant($entityClassConst)) {
                $class->addConstant($entityClassConst);
            }

            if (!$class->hasMethod('__construct')) {
                /**
                 * public function __construct(EntityManagerInterface $em)
                 * {
                 *     parent::__construct($em, self::ENTITY_CLASS, self::ALIAS);
                 * }.
                 **/
                $parameterModel = new ParameterModel('em', new ClassType(EntityManagerInterface::class));

                $constructor = new ClassMethodModel('__construct');
                $constructor->setPublic();
                $constructor->addParameter($parameterModel);

                $constructor->addStatement(
                    $constructor->toParentCall([
                        $parameterModel->toVariable(),
                        $entityClassConst->toClassConstFetchNode(true),
                        $aliasClassConst->toClassConstFetchNode(true),
                    ])
                );

                $class->addMethod($constructor);
            }
        }

        return $repository;
    }

    private function manipulateDoctrineConfig(Generator $generator, BoundingContextAreaInterface $boundingContext, ModelInterface $model): void
    {
        // @todo configurable path for doctrine config
        $manipulator = new YamlSourceManipulator(file_get_contents('config/packages/doctrine.yaml'));
        $doctrineData = $manipulator->getData();

        $boundingContextName = $boundingContext->getName();

        // @todo configurable prefix and directory
        $doctrineData['doctrine']['orm']['mappings'][$boundingContextName] = [
            'is_bundle' => false,
            'dir' => '%kernel.project_dir%/src/' . $boundingContextName . '/Domain',
            'prefix' => 'App\\' . $boundingContextName . '\\Domain',
            'alias' => $boundingContextName,
        ];

        $manipulator->setData($doctrineData);

        $generator->dumpFile('config/packages/doctrine.yaml', $manipulator->getContents());
    }
}
