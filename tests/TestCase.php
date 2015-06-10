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
namespace Kreait\EzPublish\MigrationsBundle\Tests;

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Migrations\Configuration\Configuration;
use Kreait\EzPublish\MigrationsBundle\Command\GenerateCommand;
use Kreait\EzPublish\MigrationsBundle\DependencyInjection\EzPublishMigrationsExtension;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;

class TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var EzPublishMigrationsExtension
     */
    protected $extension;

    /**
     * @var ContainerBuilder
     */
    protected $container;

    /**
     * @var string
     */
    protected $rootDir;

    /**
     * @var Filesystem
     */
    protected $fs;

    /**
     * @var Application
     */
    protected $application;

    /**
     * @var string
     */
    protected $migrationsNamespace;

    /**
     * @var string
     */
    protected $migrationUser;

    protected function setUp()
    {
        parent::setUp();

        $this->fs = new Filesystem();

        $uniqId = uniqid();
        $this->rootDir = __DIR__.'/Temporary'.$uniqId;
        $this->migrationsNamespace = 'Kreait\EzPublish\MigrationsBundle\Tests\Temporary'.$uniqId;
        $this->migrationUser = $uniqId;

        $this->extension = new EzPublishMigrationsExtension();
        $this->container = $this->getContainer();

        $this->container->registerExtension($this->extension);
        $this->container->setParameter('kernel.root_dir', $this->rootDir);

        $this->container->set('ezpublish.connection', $this->getSqliteConnection());
        $this->container->set('ezpublish.api.repository', $this->getEzRepository());

        $this->container->loadFromExtension($this->extension->getAlias());
        $this->container->setParameter('ezpublish_migrations.namespace', $this->migrationsNamespace);
        $this->container->setParameter('ezpublish_migrations.ez_user', $this->migrationUser);
        $this->container->compile();

        $this->application = $this->getApplication();
    }

    protected function tearDown()
    {
        $this->fs->remove($this->rootDir);
    }

    /**
     * Mocked repository - we don't need to test it, we just have to call its methods.
     *
     * @return \Mockery\MockInterface|\eZ\Publish\API\Repository\Repository
     */
    protected function getEzRepository()
    {
        $userService = $this->getEzUserService();
        $contentService = $this->getEzContentService();
        $contentTypeService = $this->getEzContentTypeService();
        $locationService = $this->getEzLocationService();

        $repository = \Mockery::mock('eZ\Publish\API\Repository\Repository')
            ->shouldIgnoreMissing()

            ->shouldReceive('getUserService')
            ->andReturn($userService)
            ->getMock()

            ->shouldReceive('getContentService')
            ->andReturn($contentService)
            ->getMock()

            ->shouldReceive('getContentTypeService')
            ->andReturn($contentTypeService)
            ->getMock()

            ->shouldReceive('getLocationService')
            ->andReturn($locationService)
            ->getMock()

            ->shouldReceive('setCurrentUser')
            ->andReturnNull()
            ->getMock();

        return $repository;
    }

    /**
     * @return \Mockery\MockInterface|\eZ\Publish\API\Repository\UserService
     */
    protected function getEzUserService()
    {
        $userService = \Mockery::mock('eZ\Publish\API\Repository\UserService')
            ->shouldIgnoreMissing()
            ->shouldReceive('loadUserByLogin')
            ->andReturn($this->getEzUser())
            ->getMock();

        return $userService;
    }

    /**
     * @return \Mockery\MockInterface|\eZ\Publish\API\Repository\Values\User\User
     */
    protected function getEzUser()
    {
        $user = \Mockery::mock('eZ\Publish\API\Repository\Values\User\User')
            ->shouldIgnoreMissing();

        return $user;
    }

    /**
     * @return \Mockery\MockInterface|\eZ\Publish\API\Repository\ContentService
     */
    protected function getEzContentService()
    {
        $service = \Mockery::mock('eZ\Publish\API\Repository\ContentService')
            ->shouldIgnoreMissing();

        return $service;
    }

    /**
     * @return \Mockery\MockInterface|\eZ\Publish\API\Repository\ContentTypeService
     */
    protected function getEzContentTypeService()
    {
        $service = \Mockery::mock('eZ\Publish\API\Repository\ContentTypeService')
            ->shouldIgnoreMissing();

        return $service;
    }

    /**
     * @return \Mockery\MockInterface|\eZ\Publish\API\Repository\LocationService
     */
    protected function getEzLocationService()
    {
        $service = \Mockery::mock('eZ\Publish\API\Repository\LocationService')
            ->shouldIgnoreMissing();

        return $service;
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     * @return \Doctrine\DBAL\Connection
     *
     */
    public function getSqliteConnection()
    {
        $params = ['driver' => 'pdo_sqlite', 'memory' => true];

        return DriverManager::getConnection($params);
    }

    /**
     * @return Configuration
     */
    public function getSqliteConfiguration()
    {
        return new Configuration($this->getSqliteConnection());
    }

    /**
     * @return ContainerBuilder
     */
    public function getContainer()
    {
        $ezConnection = \Mockery::mock('eZ\Publish\SPI\Persistence\Handler[getConnection]')
            ->shouldReceive('getConnection')
            ->andReturn($this->getSqliteConnection())
            ->getMock();

        $container = \Mockery::mock('Symfony\Component\DependencyInjection\ContainerBuilder[get]')
            ->shouldDeferMissing()
            ->shouldReceive('get')
            ->with('ezpublish.connection')
            ->andReturn($ezConnection)
            ->getMock();

        return $container;
    }

    /**
     * Returns the an application mock which returns a mocked kernel.
     *
     * @return \Mockery\MockInterface|Application
     */
    protected function getApplication()
    {
        $kernel = \Mockery::mock(
            'Symfony\Component\HttpKernel\HttpKernel[getContainer]',
            [new EventDispatcher(), new ControllerResolver()]
        )
            ->shouldReceive('getContainer')
            ->andReturn($this->container)
            ->getMock();

        $app = \Mockery::mock('Symfony\Component\Console\Application[getKernel]')
            ->shouldReceive('getKernel')
            ->andReturn($kernel)
            ->getMock();

        return $app;
    }

    /**
     * @throws \Exception
     *
     * @return string
     */
    protected function generateMigrationAndReturnVersionString()
    {
        $command = new GenerateCommand();
        if (!$this->application->has($command->getName())) {
            $this->application->add($command);
        }

        $params = [[]];

        $input = new ArrayInput($params);
        $output = new BufferedOutput();

        $command->run($input, $output);

        $text = $output->fetch();

        $versionString = $this->getVersionFromString($text);

        return $versionString;
    }

    /**
     * @param array $variables
     *
     * @return string
     */
    protected function generateMigrationClassFileFromTemplate(array $variables)
    {
        $placeholders = ['namespace', 'version', 'up', 'down'];

        if (!array_key_exists('version', $variables)) {
            $variables['version'] = rand(1, 999999);
        }

        $generateCommand = new GenerateCommand();
        $reflection = new \ReflectionClass($generateCommand);
        $templateProperty = $reflection->getProperty('template');
        $templateProperty->setAccessible(true);

        $template = $templateProperty->getValue($generateCommand);

        foreach ($placeholders as $placeholder) {
            if (array_key_exists($placeholder, $variables)) {
                $template = str_replace('<'.$placeholder.'>', $variables[$placeholder], $template);
            } else {
                $template = str_replace('<'.$placeholder.'>', '', $template);
            }
        }

        $fileName = $this->container->getParameter('ezpublish_migrations.dir_name').'/Version'.$variables['version'];
        $this->fs->dumpFile($fileName, $template);

        $this->assertFileExists($fileName);
    }

    /**
     * Returns the version number from a string.
     *
     * @param string $string
     *
     * @throws \RuntimeException
     * @return string
     *
     */
    protected function getVersionFromString($string)
    {
        if (preg_match('/(Version)(\d+)(.php)?/', $string, $matches)) {
            return $matches[2];
        }

        if (preg_match('/^\d+$/', $string)) {
            return trim($string);
        }

        throw new \RuntimeException("Couldn't find version in string. Please check your tests.");
    }
}
