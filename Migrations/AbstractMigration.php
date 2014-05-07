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
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class AbstractMigration extends BaseAbstractMigration implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * Sets the container
     *
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * Returns the container
     *
     * @return ContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }
}