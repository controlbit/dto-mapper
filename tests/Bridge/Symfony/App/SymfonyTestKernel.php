<?php
declare(strict_types=1);

namespace ControlBit\Dto\Tests\Bridge\Symfony\App;

use ControlBit\Dto\Bridge\Symfony\DtoBundle;
use ControlBit\Dto\Tests\Bridge\Symfony\App\Controller\DtoController;
use ControlBit\Dto\Tests\Bridge\Symfony\App\Fixtures\AppFixtures;
use DAMA\DoctrineTestBundle\DAMADoctrineTestBundle;
use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle;
use Psr\Log\NullLogger;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

final class SymfonyTestKernel extends Kernel
{
    use MicroKernelTrait;

    public function registerBundles(): iterable
    {
        return [
            new FrameworkBundle(),
            new DoctrineFixturesBundle(),
            new DoctrineBundle(),
            new DAMADoctrineTestBundle(),
            new DtoBundle(),
        ];
    }

    /**
     * @phpstan-ignore-next-line
     */
    private function configureContainer(
        ContainerConfigurator $container,
        LoaderInterface       $loader,
        ContainerBuilder      $builder,
    ): void {

        $container->extension(
            'framework',
            [
                'test'                  => true,
                'http_method_override'  => false,
                'handle_all_throwables' => true,
                'php_errors'            => [
                    'log' => true,
                ],
                'validation'            => [
                    'email_validation_mode' => 'html5',
                ],
                'translator'            => [
                    'default_path' => __DIR__ . '/translations',
                ],
            ]
        );

        $container->extension('doctrine', [
            'dbal' => [
                'driver'         => 'pdo_mysql',
                'url'            => 'mysql://db:db@database/db',
                'use_savepoints' => true,
            ],
            'orm'  => [
                'report_fields_where_declared' => true,
                'auto_generate_proxy_classes'  => true,
                'naming_strategy'              => 'doctrine.orm.naming_strategy.underscore_number_aware',
                'auto_mapping'                 => true,
                'enable_lazy_ghost_objects'    => true,
                'mappings'                     => [
                    'Tests' => [
                        'is_bundle' => false,
                        'type'      => 'attribute',
                        'dir'       => __DIR__.'/Entity',
                        'prefix'    => 'ControlBit\Dto\Tests\Bridge\Symfony\App',
                    ],
                ],
            ],
        ]);

        $container->services()->set('logger', NullLogger::class);

        $this->configureServices($container);
    }

    private function configureServices(ContainerConfigurator $container): void
    {
        $container
            ->services()
            ->set(DtoController::class)
            ->call('setContainer', [new Reference('service_container')])
            ->tag('controller.service_arguments')
        ;

        $container
            ->services()
            ->set(AppFixtures::class)
            ->tag('doctrine.fixture.orm')
        ;
    }

    /**
     * @phpstan-ignore-next-line
     */
    private function configureRoutes(RoutingConfigurator $routes): void
    {
        $routes
            ->add('dto_basic', '/dto/basic')
            ->controller(DtoController::class.'::basic')
        ;

        $routes
            ->add('dto_asserted', '/dto/asserted')
            ->controller(DtoController::class.'::asserted')
        ;
    }
}