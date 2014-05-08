<?php
/**
* This file is part of the kreait eZ Publish Migrations Bundle
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/
namespace Kreait\EzPublish\MigrationsBundle\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration as BaseAbstractMigration;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class AbstractMigration extends BaseAbstractMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * Returns the container
     *
     * @deprecated 1.0.1 Use <code>$this->container</code> instead
     * @return ContainerInterface
     */
    protected function getContainer()
    {
        return $this->container;
    }
}