<?php
/**
 * This file is part of the kreait eZ Publish Migrations Bundle
 */
namespace Kreait\EzPublish\MigrationsBundle;

use Kreait\EzPublish\MigrationsBundle\DependencyInjection\EzPublishMigrationsExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class KreaitEzPublishMigrationsBundle extends Bundle
{
    /**
     * {@inheritDoc}
     */
    public function getContainerExtension()
    {
        return new EzPublishMigrationsExtension();
    }
}
