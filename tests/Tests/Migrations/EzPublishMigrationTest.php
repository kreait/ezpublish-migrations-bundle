<?php

/*
 * This file is part of the kreait eZ Publish Migrations Bundle.
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
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

        $createStruct = $this->getMock('eZ\Publish\API\Repository\Values\Content\ContentCreateStruct');
        $contentType = $this->getMock('eZ\Publish\API\Repository\Values\ContentType\ContentType');
        $versionInfo = $this->getMock('eZ\Publish\API\Repository\Values\Content\VersionInfo');
        $content = $this->getMock('eZ\Publish\API\Repository\Values\Content\Content');

        $content
            ->expects($this->once())
            ->method('getVersionInfo')
            ->willReturn($versionInfo);

        /** @var \PHPUnit_Framework_MockObject_MockObject|\eZ\Publish\API\Repository\Repository $repository */
        $repository = $this->container->get('ezpublish.api.repository');

        /** @var \eZ\Publish\API\Repository\ContentTypeService|\PHPUnit_Framework_MockObject_MockObject $contentTypeService */
        $contentTypeService = $repository->getContentTypeService();

        $contentTypeService
            ->expects($this->any())
            ->method('loadContentTypeByIdentifier')
            ->willReturn($contentType);

        /** @var \eZ\Publish\API\Repository\ContentService|\PHPUnit_Framework_MockObject_MockObject $contentService */
        $contentService = $repository->getContentService();
        $contentService
            ->expects($this->once())
            ->method('newContentCreateStruct')
            ->willReturn($createStruct);

        $contentService
            ->expects($this->once())
            ->method('createContent')
            ->willReturn($content);

        $contentService
            ->expects($this->once())
            ->method('publishVersion')
            ->willReturn($content);

        $createStruct
            ->expects($this->once())
            ->method('setField')
            ->with('title', 'Title');

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
