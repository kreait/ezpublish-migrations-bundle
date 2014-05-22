<?php
/**
 * This file is part of the kreait eZ Publish Migrations Bundle
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Kreait\EzPublish\Tests\Command;

use Kreait\EzPublish\MigrationsBundle\Command\ExecuteCommand;
use Kreait\EzPublish\MigrationsBundle\Tests\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @group commands
 */
class ExecuteCommandTest extends TestCase
{
    public function testExecute()
    {
        $versionString = $this->generateMigrationAndReturnVersionString();

        $command = new ExecuteCommand();
        $this->application->add( $command );

        $tester = new CommandTester( $command );
        $tester->execute(
            array(
                'command' => $command->getName(),
                'version' => $versionString,
                '--no-interaction' => true,
            ),
            array(
                'interactive' => false
            )
        );

        $output = $tester->getDisplay();

        $this->assertRegExp( '/\+\+ migrated/', $output );
    }
}
