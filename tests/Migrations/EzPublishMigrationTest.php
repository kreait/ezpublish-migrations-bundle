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
namespace Kreait\EzPublish\MigrationsBundle\Tests\Migrations;

use Doctrine\DBAL\Migrations\Version;
use Kreait\EzPublish\MigrationsBundle\Tests\TestCase;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

class EzPublishMigrationTest extends TestCase
{
    /**
     * @param string $direction "up" or "down"
     * @dataProvider directionProvider
     */
    public function testGetMigrationUser($direction)
    {
        $versionString = $this->generateMigrationAndReturnVersionString();
        $namespace = $this->container->getParameter('ezpublish_migrations.namespace');
        $config = $this->getSqliteConfiguration();

        $fullClassName = $namespace.'\\Version'.$versionString;
        $filePath = $this->container->getParameter('ezpublish_migrations.dir_name').'/Version'.$versionString.'.php';

        $this->assertTrue($this->fs->exists($filePath));

        require $filePath;

        $version = new Version($config, $versionString, $fullClassName);

        $migration = $version->getMigration();
        if ($migration instanceof ContainerAwareInterface) {
            $migration->setContainer($this->container);
        }

        $version->execute($direction, true);

        $this->assertAttributeEquals($this->migrationUser, 'defaultMigrationUser', $migration);
        $this->assertAttributeEquals($this->migrationUser, 'currentMigrationUser', $migration);
    }

    /**
     * @param string $direction "up" or "down"
     * @dataProvider directionProvider
     */
    public function testMigration($direction)
    {
        $versionString = $this->generateMigrationAndReturnVersionString();
        $namespace = $this->container->getParameter('ezpublish_migrations.namespace');
        $config = $this->getSqliteConfiguration();

        $fullClassName = $namespace.'\\Version'.$versionString;
        $filePath = $this->container->getParameter('ezpublish_migrations.dir_name').'/Version'.$versionString.'.php';

        $this->assertTrue($this->fs->exists($filePath));

        require $filePath;

        $version = new Version($config, $versionString, $fullClassName);

        $migration = $version->getMigration();
        if ($migration instanceof ContainerAwareInterface) {
            $migration->setContainer($this->container);
        }

        $version->execute($direction, true);

        $this->assertInstanceOf($fullClassName, $migration);
    }

    public function testCreateContent()
    {
        $config = $this->getSqliteConfiguration();

        $version = new Version($config, '1', 'Kreait\EzPublish\MigrationsBundle\Tests\Fixtures\Version1');
        $migration = $version->getMigration();
        if ($migration instanceof ContainerAwareInterface) {
            $migration->setContainer($this->container);
        }

        $createStruct = \Mockery::mock('eZ\Publish\API\Repository\Values\Content\ContentCreateStruct');
        $contentType = \Mockery::mock('eZ\Publish\API\Repository\Values\ContentType\ContentType');
        $versionInfo = \Mockery::mock('eZ\Publish\API\Repository\Values\Content\VersionInfo');
        $content = \Mockery::mock('eZ\Publish\API\Repository\Values\Content\Content');

        $content->shouldReceive('getVersionInfo')->andReturn($versionInfo);

        /** @var @var \Mockery\MockInterface|\eZ\Publish\API\Repository\Repository $repository */
        $repository = $this->container->get('ezpublish.api.repository');
        $contentTypeService = $repository->getContentTypeService();
        $contentTypeService->shouldReceive('loadContentTypeByIdentifier')->andReturn($contentType);

        $contentService = $repository->getContentService();
        $contentService->shouldReceive('newContentCreateStruct')->andReturn($createStruct);
        $contentService->shouldReceive('createContent')->andReturn($content);
        $contentService->shouldReceive('publishDraft')->andReturn($content);

        $createStruct->shouldReceive('setField')->withArgs(['title', 'Title'])->times(1);

        $version->execute('up', true);
    }

    public function directionProvider()
    {
        return [
            ['up'],
            ['down'],
        ];
    }
}
