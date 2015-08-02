<?php

/*
 * This file is part of the kreait eZ Publish Migrations Bundle.
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Kreait\EzPublish\MigrationsBundle\Tests\DependencyInjection;

use Kreait\EzPublish\MigrationsBundle\Tests\TestCase;

class EzPublishMigrationsExtensionTest extends TestCase
{
    /**
     * Normally, we would test with a data provider, but we can't use the computed kernel root dir in it, so we
     * define everything inside.
     */
    public function testDefaultConfiguration()
    {
        $checks = [
            'dir_name' => $this->rootDir.'/EzPublishMigrations',
            'namespace' => $this->migrationsNamespace,
            'table_name' => 'ezmigration_versions',
            'name' => 'Application Migrations',
            'ez_user' => $this->migrationUser,
        ];

        foreach ($checks as $key => $value) {
            $this->assertEquals($value, $this->container->getParameter("{$this->extension->getAlias()}.{$key}"));
        }
    }
}
