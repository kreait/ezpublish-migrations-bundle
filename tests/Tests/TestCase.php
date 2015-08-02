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
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Filesystem\Filesystem;

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
        $this->container = new ContainerBuilder();

        $this->container->registerExtension($this->extension);
        $this->container->setParameter('kernel.root_dir', $this->rootDir);

        $this->container->set('ezpublish.connection', $this->getEzPersistenceHandler());
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
     * @return \PHPUnit_Framework_MockObject_MockObject|\eZ\Publish\API\Repository\Repository
     */
    protected function getEzRepository()
    {
        $repository = $this->getMock('eZ\Publish\API\Repository\Repository');

        $repository
            ->expects($this->any())
            ->method('getUserService')
            ->willReturn($this->getEzUserService());

        $repository
            ->expects($this->any())
            ->method('getContentService')
            ->willReturn($this->getEzContentService());

        $repository
            ->expects($this->any())
            ->method('getContentTypeService')
            ->willReturn($this->getEzContentTypeService());

        $repository
            ->expects($this->any())
            ->method('getLocationService')
            ->willReturn($this->getEzLocationService());

        $repository
            ->expects($this->any())
            ->method('setCurrentUser');

        return $repository;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\eZ\Publish\API\Repository\UserService
     */
    protected function getEzUserService()
    {
        $userService = $this->getMock('eZ\Publish\API\Repository\UserService');

        $userService
            ->expects($this->any())
            ->method('loadUserByLogin')
            ->willReturn($this->getEzUser());

        return $userService;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\eZ\Publish\API\Repository\Values\User\User
     */
    protected function getEzUser()
    {
        $user = $this->getMockBuilder('eZ\Publish\API\Repository\Values\User\User')
            ->getMock();

        return $user;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\eZ\Publish\API\Repository\ContentService
     */
    protected function getEzContentService()
    {
        $service = $this->getMock('eZ\Publish\API\Repository\ContentService');

        return $service;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\eZ\Publish\API\Repository\ContentTypeService
     */
    protected function getEzContentTypeService()
    {
        return $this->getMock('eZ\Publish\API\Repository\ContentTypeService');
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\eZ\Publish\API\Repository\LocationService
     */
    protected function getEzLocationService()
    {
        return $this->getMock('eZ\Publish\API\Repository\LocationService');
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     *
     * @return \Doctrine\DBAL\Connection
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

    public function getEzPersistenceHandler()
    {
        $handler = $this->getMockBuilder('eZ\Publish\SPI\Persistence\Handler')
            ->setMethods(['getConnection'])
            ->getMockForAbstractClass();

        $handler
            ->expects($this->any())
            ->method('getConnection')
            ->willReturn($this->getSqliteConnection());

        return $handler;
    }

    /**
     * Returns the an application mock which returns a mocked kernel.
     *
     * @return Application
     */
    protected function getApplication()
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject|\Symfony\Component\HttpKernel\KernelInterface $kernel */
        $kernel = $this->getMock('Symfony\Component\HttpKernel\KernelInterface');

        $kernel
            ->expects($this->any())
            ->method('getContainer')
            ->willReturn($this->container);

        $app = new Application($kernel);

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
     *
     * @return string
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
