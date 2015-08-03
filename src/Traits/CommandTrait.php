<?php

/*
 * This file is part of the kreait eZ Publish Migrations Bundle.
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Kreait\EzPublish\MigrationsBundle\Traits;

use Doctrine\DBAL\Migrations\Configuration\Configuration;
use Doctrine\DBAL\Migrations\OutputWriter;
use Doctrine\DBAL\Migrations\Version;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

trait CommandTrait
{
    protected function configureMigrations(ContainerInterface $container, Configuration $configuration)
    {
        $dir = $container->getParameter('ezpublish_migrations.dir_name');
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }

        $configuration->setMigrationsNamespace($container->getParameter('ezpublish_migrations.namespace'));
        $configuration->setMigrationsDirectory($dir);
        $configuration->registerMigrationsFromDirectory($dir);
        $configuration->setName($container->getParameter('ezpublish_migrations.name'));
        $configuration->setMigrationsTableName($container->getParameter('ezpublish_migrations.table_name'));

        self::injectContainerToMigrations($container, $configuration->getMigrations());
    }

    /**
     * Injects the container to migrations aware of it.
     *
     * @param ContainerInterface $container
     * @param Version[]          $versions
     */
    protected function injectContainerToMigrations(ContainerInterface $container, array $versions)
    {
        foreach ($versions as $version) {
            $migration = $version->getMigration();
            if ($migration instanceof ContainerAwareInterface) {
                $migration->setContainer($container);
            }
        }
    }

    /**
     * Generates a doctrine configuration object with eZ Publish's database connection.
     *
     * @param ContainerInterface $container
     * @param OutputInterface    $output
     *
     * @return \Doctrine\DBAL\Migrations\Configuration\Configuration
     */
    protected function getBasicConfiguration(ContainerInterface $container, OutputInterface $output)
    {
        $outputWriter = new OutputWriter(
            function ($message) use ($output) {
                // @codeCoverageIgnoreStart
                if (!(stripos($message, 'but did not result in any sql statements') !== false)) {
                    return $output->writeln($message);
                }
                return null;
                // @codeCoverageIgnoreEnd
            }
        );

        return new Configuration($container->get('ezpublish.connection')->getConnection(), $outputWriter);
    }
}
