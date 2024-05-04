<?php
declare(strict_types=1);

require_once(__DIR__.'/../vendor/autoload.php');

use ControlBit\Dto\Tests\Bridge\Symfony\App\SymfonyTestKernel;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Filesystem\Filesystem;

$bootstrap = getenv('BOOTSTRAP') ?: 'yes' ;

if (!\in_array(\trim($bootstrap), ['false', '0', 'no'], true)) {

    (new Filesystem())->remove(
        [
            __DIR__.'/../var/cache',
            __DIR__.'/../var/log',
        ]
    );

    $kernel = new SymfonyTestKernel('test', false);

    $kernel->boot();

    $output   = new ConsoleOutput();
    $commands = [
        [
            'command' => 'doctrine:schema:drop',
            '--env'   => 'test',
            '--force' => null,
        ],
        [
            'command' => 'doctrine:schema:create',
            '--env'   => 'test',
        ],
        [
            'command'          => 'doctrine:fixtures:load',
            '--env'            => 'test',
            '--no-interaction' => null,
        ],
    ];

    $application = new Application($kernel);

    $application->setAutoExit(false);
    try {
        foreach ($commands as $command) {
            if (Command::SUCCESS !== $application->run(new ArrayInput($command), $output)) {
                throw new \RuntimeException('Failed to prepare for tests');
            }
        }
    } finally {
        $kernel->shutdown();
    }
}
