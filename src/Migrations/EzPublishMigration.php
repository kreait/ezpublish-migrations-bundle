<?php

/*
 * This file is part of the kreait eZ Publish Migrations Bundle.
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Kreait\EzPublish\MigrationsBundle\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use eZ\Publish\API\Repository\Exceptions\ContentFieldValidationException;
use eZ\Publish\API\Repository\Exceptions\ContentValidationException;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Values\Content\Content;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

abstract class EzPublishMigration extends AbstractMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * The username of the default migration user.
     *
     * @var string
     */
    private $defaultMigrationUser;

    /**
     * @var \eZ\Publish\API\Repository\Repository
     */
    protected $repository;

    /**
     * Initializes eZ Publish related service shortcuts.
     */
    protected function pre()
    {
        $this->repository = $this->container->get('ezpublish.api.repository');

        $this->defaultMigrationUser = $this->container->getParameter('ezpublish_migrations.ez_migrations_user');

        $this->setDefaultMigrationUser();
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
     * Sets the current user to the user with the given name.
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
     * Sets the current ez user the the default migration user.
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
            $this->repository->getUserService()->loadUserByLogin($username)
        );
    }

    /**
     * Helper to quickly create content.
     *
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
        $contentService = $this->repository->getContentService();
        $locationService = $this->repository->getLocationService();
        $contentTypeService = $this->repository->getContentTypeService();

        $contentType = $contentTypeService->loadContentTypeByIdentifier($contentTypeIdentifier);
        $contentCreateStruct = $contentService->newContentCreateStruct($contentType, $languageCode);

        foreach ($fields as $key => $value) {
            $contentCreateStruct->setField($key, $value);
        }

        $locationCreateStruct = $locationService->newLocationCreateStruct($parentLocationId);
        $draft = $contentService->createContent($contentCreateStruct, [$locationCreateStruct]);
        $content = $contentService->publishVersion($draft->getVersionInfo());

        return $content;
    }
}
