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

use Kreait\EzPublish\MigrationsBundle\KreaitEzPublishMigrationsBundle;

class KreaitEzPublishMigrationsBundleTest extends \PHPUnit_Framework_TestCase
{
    public function testGetExtension()
    {
        $bundle = new KreaitEzPublishMigrationsBundle();

        $this->assertInstanceOf(
            'Kreait\EzPublish\MigrationsBundle\DependencyInjection\EzPublishMigrationsExtension',
            $bundle->getContainerExtension()
        );
    }
}
