<?php

/*
 * This file is part of the kreait eZ Publish Migrations Bundle.
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Kreait\EzPublish\MigrationsBundle;

use Kreait\EzPublish\MigrationsBundle\DependencyInjection\EzPublishMigrationsExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class KreaitEzPublishMigrationsBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new EzPublishMigrationsExtension();
    }
}
