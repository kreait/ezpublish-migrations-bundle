<?php
/**
* This file is part of the kreait eZ Publish Migrations Bundle
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/
namespace Kreait\EzPublish\MigrationsBundle\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration as BaseAbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class AbstractMigration extends BaseAbstractMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * The username of the default migration user
     *
     * @var string
     */
    private $defaultMigrationUser;

    /**
     * The username of the current migration user
     *
     * @var string
     */
    private $currentMigrationUser;

    /**
     * @var \eZ\Publish\API\Repository\Repository
     */
    protected $repository;

    /**
     * @var \eZ\Publish\API\Repository\UserService
     */
    private $userService;

    /**
     * Initializes eZ Publish related service shortcuts
     *
     * @param Schema $schema
     */
    protected function pre(Schema $schema)
    {
        $this->repository = $this->container->get( 'ezpublish.api.repository' );
        $this->userService = $this->repository->getUserService();

        $this->defaultMigrationUser = $this->container->getParameter( 'ezpublish_migrations.ez_user');
        $this->currentMigrationUser = $this->defaultMigrationUser;

        $this->setDefaultMigrationUser();
    }

    /**
     * {@inheritDoc}
     */
    public function preUp(Schema $schema)
    {
        parent::preUp( $schema );
        $this->pre( $schema );
    }

    /**
     * {@inheritDoc}
     */
    public function preDown(Schema $schema)
    {
        parent::preDown( $schema );
        $this->pre( $schema );
    }

    /**
     * Sets the current user to the user with the given name
     *
     * @param string $username
     */
    protected function changeMigrationUser($username)
    {
        $this->setMigrationUser($username);
    }

    /**
     * Sets the current user to the default migration user.
     */
    protected function restoreDefaultMigrationUser()
    {
        $this->setDefaultMigrationUser();
    }

    /**
     * Returns the container
     *
     * @deprecated 1.0.1 Use <code>$this->container</code> instead
     * @codeCoverageIgnore
     * @return ContainerInterface
     */
    protected function getContainer()
    {
        return $this->container;
    }

    /**
     * Sets the current ez user the the default migration user
     */
    private function setDefaultMigrationUser()
    {
        $this->setMigrationUser($this->defaultMigrationUser);
    }

    /**
     * Sets the current ez user to the user with the given user name.
     *
     * @param string $username
     */
    private function setMigrationUser($username)
    {
        $this->repository->setCurrentUser(
            $this->userService->loadUserByLogin( $username )
        );
    }
}
