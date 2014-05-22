<?php
/**
 * This file is part of the kreait eZ Publish Migrations Bundle
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Kreait\EzPublish\Tests\Command;

use Kreait\EzPublish\MigrationsBundle\Command\LatestCommand;
use Kreait\EzPublish\MigrationsBundle\Tests\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @group commands
 */
class LatestCommandTest extends TestCase
{
    /**
     * @ runInSeparateProcess
     */
    public function testExecute()
    {
        $versionString = $this->generateMigrationAndReturnVersionString();

        $command = new LatestCommand();
        $this->application->add( $command );

        $tester = new CommandTester( $command );
        $tester->execute(
            array(
                'command' => $command->getName(),
            )
        );

        $this->assertEquals( $versionString, $this->getVersionFromString( $tester->getDisplay() ) );
    }
}
