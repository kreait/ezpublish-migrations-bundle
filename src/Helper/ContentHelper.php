<?php

/*
 * This file is part of the kreait eZ Publish Migrations Bundle.
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Kreait\EzPublish\MigrationsBundle\Helper;

use eZ\Publish\API\Repository\Exceptions\ContentFieldValidationException;
use eZ\Publish\API\Repository\Exceptions\ContentValidationException;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Values\Content\Content;
use Kreait\EzPublish\MigrationsBundle\Helper;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ContentHelper implements Helper
{
    use HelperTrait;

    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getName()
    {
        return 'content';
    }

    /**
     * Helper to quickly create content.
     *
     * @see https://github.com/ezsystems/CookbookBundle/blob/master/Command/CreateContentCommand.php eZ Publish Cookbook
     *
     * @param int    $parentLocationId      The parent location ID, e.g. 2
     * @param string $contentTypeIdentifier The content type identifier, e.g. 'article'
     * @param string $languageCode          The language code, e.g. 'eng-GB'
     * @param array  $fields                The fields as an associative array [$identifier => $value]
     *
     * @throws NotFoundException               If the content type or parent location could not be found
     * @throws ContentFieldValidationException If an invalid field value has been provided
     * @throws ContentValidationException      If a required field is missing or empty
     *
     * @return Content
     */
    public function createContent($parentLocationId, $contentTypeIdentifier, $languageCode, array $fields)
    {
        /** @var \eZ\Publish\API\Repository\Repository $repository */
        $repository = $this->container->get('ezpublish.api.repository');

        $contentService = $repository->getContentService();
        $locationService = $repository->getLocationService();
        $contentTypeService = $repository->getContentTypeService();

        $contentType = $contentTypeService->loadContentTypeByIdentifier($contentTypeIdentifier);
        $contentCreateStruct = $contentService->newContentCreateStruct($contentType, $languageCode);

        foreach ($fields as $key => $value) {
            $contentCreateStruct->setField($key, $value);
        }

        $locationCreateStruct = $locationService->newLocationCreateStruct($parentLocationId);
        $draft = $contentService->createContent($contentCreateStruct, [$locationCreateStruct]);

        return $contentService->publishVersion($draft->getVersionInfo());
    }
}
