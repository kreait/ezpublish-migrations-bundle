<?php
/**
 * This file is part of the kreait eZ Publish Migrations Bundle
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Kreait\EzPublish\MigrationsBundle\Command;

use Doctrine\DBAL\Migrations\Tools\Console\Command\VersionCommand as BaseVersionCommand;
use Kreait\EzPublish\MigrationsBundle\Traits\CommandTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Command for generating new blank migration classes.
 */
class VersionCommand extends BaseVersionCommand
{
    use CommandTrait;

    protected function configure()
    {
        BaseVersionCommand::configure();

        $this->setName('ezpublish:migrations:version');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var ContainerInterface $container */
        $container = $this->getApplication()->getKernel()->getContainer();

        $this->setMigrationConfiguration($this->getBasicConfiguration($container, $output));

        $configuration = $this->getMigrationConfiguration($input, $output);
        $this->configureMigrations($container, $configuration);

        BaseVersionCommand::execute($input, $output);
    }
}
