<?php

/*
 * This file is part of the kreait eZ Publish Migrations Bundle.
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Kreait\EzPublish\MigrationsBundle\Tests\Command;

use Kreait\EzPublish\MigrationsBundle\Command\VersionCommand;
use Kreait\EzPublish\MigrationsBundle\Tests\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @group commands
 */
class VersionCommandTest extends TestCase
{
    public function testExecuteAddVersion()
    {
        $versionString = $this->generateMigrationAndReturnVersionString();

        $command = new VersionCommand();
        $this->application->add($command);

        $tester = new CommandTester($command);
        $tester->execute(
            [
                'command' => $command->getName(),
                'version' => $versionString,
                '--add' => true,
                '--no-interaction' => true,
            ],
            [
                'interactive' => false,
            ]
        );

        $output = $tester->getDisplay();

        $this->assertNotEmpty($output);
    }
}
