<?php
/**
 * This file is part of the kreait eZ Publish Migrations Bundle
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Kreait\Tests\DependencyInjection;

use Kreait\EzPublish\MigrationsBundle\Tests\TestCase;

class EzPublishMigrationsExtensionTest extends TestCase
{
    /**
     * Normally, we would test with a data provider, but we can't use the computed kernel root dir in it, so we
     * define everything inside
     */
    public function testDefaultConfiguration()
    {
        $checks = array(
            'dir_name' => $this->rootDir . '/EzPublishMigrations',
            'namespace' => $this->migrationsNamespace,
            'table_name' => 'ezmigration_versions',
            'name' => 'Application Migrations',
        );

        foreach ( $checks as $key => $value )
        {
            $this->assertEquals( $value, $this->container->getParameter( "{$this->extension->getAlias()}.{$key}" ) );
        }
    }
}
