<?php

namespace Drupal\menuql\Plugin\GraphQL\Schema;

use Drupal\graphql\GraphQL\ResolverBuilder;
use Drupal\graphql\GraphQL\ResolverRegistry;
use Drupal\graphql\Plugin\GraphQL\Schema\SdlSchemaPluginBase;
use Drupal\node\NodeInterface;
// use Drupal\Core\Plugin\Contex\ContextDefinition;

/**
 * @Schema(
 *   id = "menuql",
 *   name = "Menu data schema"
 * )
 */
class MenuqlSchema extends SdlSchemaPluginBase {

  /**
   * {@inheritdoc}
   */
  public function getResolverRegistry() {
    $builder = new ResolverBuilder();
    $registry = new ResolverRegistry();

    // Tell GraphQL how to resolve types of a common interface.
    $registry->addTypeResolver('NodeInterface', function ($value) {
      if ($value instanceof NodeInterface) {
        switch($value->bundle()) {
          case 'menu': return 'Menu';
        }
      }
      throw new Error('Could not resolve content type.');
    });

    $registry->addFieldResolver('Query', 'menu',
      $builder->produce('entity_load')
        ->map('type', $builder->fromValue('node'))
        ->map('bundles', $builder->fromValue(['menu']))
        ->map('id', $builder->fromArgument('id'))
    );

    $registry->addFieldResolver('Menu', 'id',
      $builder->produce('entity_id')
        ->map('entity', $builder->fromParent())
    );

    $registry->addFieldResolver('Menu', 'title',
      $builder->produce('entity_label')
        ->map('entity', $builder->fromParent())
    );

    return $registry;
  }

}
