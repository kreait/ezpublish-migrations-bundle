<?php
/**
 * This file is part of the kreait eZ Publish Migrations Bundle
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Kreait\EzPublish\MigrationsBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * @var string
     */
    private $rootIdentifier;

    /**
     * @param string $rootIdentifier
     */
    public function __construct($rootIdentifier)
    {
        $this->rootIdentifier = $rootIdentifier;
    }

    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root($this->rootIdentifier);

        $rootNode
            ->children()
            ->scalarNode('dir_name')->defaultValue('%kernel.root_dir%/EzPublishMigrations')->cannotBeEmpty()->end()
            ->scalarNode('namespace')->defaultValue('Application\Migrations')->cannotBeEmpty()->end()
            ->scalarNode('table_name')->defaultValue('ezmigration_versions')->cannotBeEmpty()->end()
            ->scalarNode('name')->defaultValue('Application Migrations')->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
