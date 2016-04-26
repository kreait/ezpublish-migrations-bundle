<?php

/*
 * This file is part of the kreait eZ Publish Migrations Bundle.
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Kreait\EzPublish\MigrationsBundle\Migrations;

use Doctrine\DBAL\Migrations\AbortMigrationException;
use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use eZ\Publish\API\Repository\Exceptions\ContentFieldValidationException;
use eZ\Publish\API\Repository\Exceptions\ContentValidationException;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\User\User;
use Kreait\EzPublish\MigrationsBundle\Helper\ContentHelper;
use Kreait\EzPublish\MigrationsBundle\Helper\HelperSet;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * @property ContentHelper $contentHelper
 */
abstract class EzPublishMigration extends AbstractMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @var \eZ\Publish\API\Repository\Repository
     */
    protected $repository;

    protected $helperSet;

    protected function getHelperSet()
    {
        if (!$this->helperSet) {
            $this->helperSet = new HelperSet([
                new ContentHelper($this->container),
            ]);
        }

        return $this->helperSet;
    }

    public function __get($name)
    {
        if (preg_match('/^(?P<name>.+)Helper$/i', $name, $matches)) {
            return $this->getHelperSet()->get(lcfirst($matches['name']));
        }

        trigger_error('Call to undefined property '.__CLASS__.'::'.$name, E_USER_NOTICE);

        return;
    }

    /**
     * Returns the helper with the given name.
     *
     * @param string $name
     *
     * @return \Kreait\EzPublish\MigrationsBundle\Helper
     */
    protected function getHelper($name)
    {
        return $this->getHelperSet()->get($name);
    }

    /**
     * Initializes eZ Publish related service shortcuts.
     *
     * @throws AbortMigrationException if the repository could not be retrieved
     */
    protected function pre()
    {
        try {
            $this->repository = $this->container->get('ezpublish.api.repository');
        } catch (\Exception $e) {
            throw new AbortMigrationException($e->getMessage(), $e->getCode(), $e);
        }

        try {
            $this->setDefaultUser();
        } catch (\Exception $e) {
            throw new AbortMigrationException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function preUp(Schema $schema)
    {
        parent::preUp($schema);
        $this->pre();
    }

    public function preDown(Schema $schema)
    {
        parent::preDown($schema);
        $this->pre();
    }

    /**
     * Sets the current ez user.
     *
     * @param int|string|User $user
     *
     * @throws NotFoundException if the given user does not exist.
     */
    protected function setCurrentUser($user)
    {
        if (is_numeric($user)) {
            $user = $this->repository->getUserService()->loadUser((int) $user);
        } elseif (!($user instanceof User)) {
            $user = $this->repository->getUserService()->loadUserByLogin($user);
        }

        $this->repository->setCurrentUser($user);
    }

    /**
     * Restores the current user to be the defined default user.
     *
     * @throws \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException if the default migrations user has not been defined
     * @throws NotFoundException                                                         if the default user does not exist.
     */
    protected function restoreDefaultUser()
    {
        $this->setDefaultUser();
    }

    /**
     * Sets the current ez user the the default migration user.
     *
     * @throws \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException if the default migrations user has not been defined
     * @throws NotFoundException                                                         if the default user does not exist.
     */
    private function setDefaultUser()
    {
        $this->setCurrentUser($this->container->getParameter('ezpublish_migrations.ez_migrations_user'));
    }

    /**
     * Sets the current user to the user with the given name.
     *
     * @deprecated 4.1.0 use $this->setCurrentUser($userNameOrId) instead
     *
     * @param string $username
     *
     * @throws NotFoundException if the given user does not exist.
     */
    protected function changeMigrationUser($username)
    {
        $this->setCurrentUser($username);
    }

    /**
     * Sets the current user to the default migration user.
     *
     * @deprecated 4.1.0 use $this->restoreDefaultUser() instead
     *
     * @throws \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException if the default migrations user has not been defined
     * @throws NotFoundException                                                         if the default user does not exist.
     */
    protected function restoreDefaultMigrationUser()
    {
        $this->setDefaultUser();
    }

    /**
     * Helper to quickly create content.
     *
     * @deprecated 4.1.0 Use $this->getHelper('content')->createContent() instead
     * @see https://github.com/ezsystems/CookbookBundle/blob/master/Command/CreateContentCommand.php eZ Publish Cookbook
     *
     * Usage:
     * <code>
     * $this->createContent(2, 'folder', 'eng-GB', [
     *     'title' => 'Folder Title',
     * ]);
     * </code>
     *
     * @param int    $parentLocationId
     * @param string $contentTypeIdentifier
     * @param string $languageCode
     * @param array  $fields
     *
     * @throws NotFoundException               If the content type or parent location could not be found
     * @throws ContentFieldValidationException If an invalid field value has been provided
     * @throws ContentValidationException      If a required field is missing or empty
     *
     * @return Content
     */
    protected function createContent($parentLocationId, $contentTypeIdentifier, $languageCode, array $fields)
    {
        return $this->contentHelper->createContent($parentLocationId, $contentTypeIdentifier, $languageCode, $fields);
    }
}
