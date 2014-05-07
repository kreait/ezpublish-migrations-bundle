<?php
/**
 * This file is part of the kreait eZ Publish Migrations Bundle
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Kreait\EzPublish\MigrationsBundle\Command;

use Kreait\EzPublish\MigrationsBundle\Traits\CommandTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\DBAL\Migrations\Tools\Console\Command\ExecuteCommand as BaseExecuteCommand;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Command for executing single migrations up or down manually.
 */
class ExecuteCommand extends BaseExecuteCommand
{
    use CommandTrait;

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        BaseExecuteCommand::configure();

        $this->setName('ezpublish:migrations:execute');
    }

    /**
     * {@inheritDoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var ContainerInterface $container */
        $container = $this->getApplication()->getKernel()->getContainer();

        $this->setMigrationConfiguration($this->getBasicConfiguration($container, $output));

        $configuration = $this->getMigrationConfiguration($input, $output);
        $this->configureMigrations($container, $configuration);

        BaseExecuteCommand::execute($input, $output);
    }
}
