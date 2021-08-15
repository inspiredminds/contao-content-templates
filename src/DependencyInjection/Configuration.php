<?php

declare(strict_types=1);

/*
 * This file is part of the Contao Content Templates extension.
 *
 * (c) inspiredminds
 *
 * @license LGPL-3.0-or-later
 */

namespace InspiredMinds\ContaoContentTemplates\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('contao_content_templates');
        $treeBuilder
            ->getRootNode()
            ->children()
                ->booleanNode('delete_empty_articles')
                    ->info('Whether to automatically delete any empty articles in the target page when applying a template.')
                    ->defaultFalse()
                ->end()
                ->arrayNode('copy_properties')
                    ->info('Properties to copy when re-applying a content template.')
                    ->children()
                        ->arrayNode('tl_article')
                            ->info('Properties to copy for articles.')
                            ->example(['customTpl', 'protected', 'groups', 'guests'])
                            ->prototype('scalar')->end()
                            ->defaultValue([])
                        ->end()
                        ->arrayNode('tl_content')
                            ->info('Properties to copy for content elements.')
                            ->example(['customTpl', 'protected', 'groups', 'guests', 'size', 'floating', 'fullsize'])
                            ->prototype('scalar')->end()
                            ->defaultValue([])
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
