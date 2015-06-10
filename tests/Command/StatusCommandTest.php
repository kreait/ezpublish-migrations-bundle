<?php

/*
 * This file is part of the kreait eZ Publish Migrations Bundle.
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 */

/**
 * This file is part of the kreait eZ Publish Migrations Bundle.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Kreait\EzPublish\MigrationsBundle\Tests\Command;

use Kreait\EzPublish\MigrationsBundle\Command\StatusCommand;
use Kreait\EzPublish\MigrationsBundle\Tests\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @group commands
 */
class StatusCommandTest extends TestCase
{
    /**
     * @param int $numberOfVersions
     * @dataProvider commandInputsProvider
     */
    public function testExecute($numberOfVersions)
    {
        $versions = [];
        for ($i = 0; $i < $numberOfVersions; $i++) {
            $versions[] = $this->generateMigrationAndReturnVersionString();
            if ($numberOfVersions > 1) {
                sleep(1); // We have to wait for one second to get a new version :)
            }
        }

        $command = new StatusCommand();
        $this->application->add($command);

        $tester = new CommandTester($command);
        $tester->execute(['command' => $command->getName()]);

        $output = $tester->getDisplay();

        $pattern = sprintf('/Name:\s+(%s)/', preg_quote($this->container->getParameter('ezpublish_migrations.name')));
        $this->assertRegExp($pattern, $output);

        $pattern = sprintf('/Version Table Name:\s+(%s)/', preg_quote($this->container->getParameter('ezpublish_migrations.table_name')));
        $this->assertRegExp($pattern, $output);

        $pattern = sprintf('/Migrations Namespace:\s+(%s)/', preg_quote($this->container->getParameter('ezpublish_migrations.namespace'), '/'));
        $this->assertRegExp($pattern, $output);

        $pattern = sprintf('/Migrations Directory:\s+(%s)/', preg_quote($this->container->getParameter('ezpublish_migrations.dir_name'), '/'));
        $this->assertRegExp($pattern, $output);

        if (count($versions)) {
            $pattern = sprintf('/Latest Version:(.+)\(%s\)/', preg_quote(end($versions)));
        } else {
            $pattern = '/Latest Version:(.+)0/';
        }
        $this->assertRegExp($pattern, $output);

        $pattern = sprintf('/New Migrations:\s+(%s)/', preg_quote(count($versions), '/'));
        $this->assertRegExp($pattern, $output);
    }

    public function commandInputsProvider()
    {
        return [
            [0],
            [1],
        ];
    }
}
