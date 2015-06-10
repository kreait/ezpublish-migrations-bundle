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

use Kreait\EzPublish\MigrationsBundle\Command\GenerateCommand;
use Kreait\EzPublish\MigrationsBundle\Tests\TestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * @group commands
 */
class GenerateCommandTest extends TestCase
{
    /**
     * @param array $commandInputs
     * @dataProvider commandInputsProvider
     * @ runInSeparateProcess
     */
    public function testExecute(array $commandInputs)
    {
        $application = $this->getApplication();

        $command = new GenerateCommand();
        $application->add($command);

        $commandInputs = array_merge(
            [
                'command' => $command->getName(),
            ], $commandInputs
        );

        $tester = new CommandTester($command);
        $tester->execute($commandInputs);

        $output = $tester->getDisplay();
        $versionString = $this->getVersionFromString($output);

        $expectedPath = $this->container->getParameter('ezpublish_migrations.dir_name');
        $this->assertFileExists($expectedPath);

        $finder = new Finder();
        // A new file should have been created with the following attributes:
        // - The file name starts with 'Version'
        // - The file contains a use statement using our AbstractMigration
        $files = $finder
            ->in($expectedPath)
            ->files()
            ->name("Version{$versionString}.php")
            ->contains('use Kreait\EzPublish\MigrationsBundle\Migrations\AbstractMigration as EzPublishMigration;');

        // Used for generating fixtures during development
        // $this->writeFixtures($files);

        $this->assertEquals(1, $files->count());
    }

    public function commandInputsProvider()
    {
        return [
            [
                [],
            ],
            [
                ['--editor-cmd' => 'echo '],
            ],
        ];
    }

    /**
     * Copies generated fixtures into the _fixtures dir. Only used during development.
     *
     * @param Finder $files
     */
    protected function writeFixtures(Finder $files)
    {
        $fs = new Filesystem();
        foreach ($files as $file) {
            /* @var SplFileInfo $file */
            $fs->copy($file->getRealPath(), __DIR__.'../_fixtures/'.$file->getBasename());
        }
    }
}
