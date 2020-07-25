<?php

namespace Drupal\menuql\Plugin\GraphQL\Schema;

use Drupal\graphql\GraphQL\ResolverBuilder;
use Drupal\graphql\GraphQL\ResolverRegistry;
use Drupal\graphql\Plugin\GraphQL\Schema\SdlSchemaPluginBase;
use Drupal\node\NodeInterface;
use Drupal\image\Entity\ImageStyle;

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

    $this->addQueryFields($registry, $builder);
    $this->addMenuFields($registry, $builder);
    $this->addGroupFields($registry, $builder);
    $this->addItemFields($registry, $builder);

    return $registry;
  }

  /**
   * @param \Drupal\graphql\GraphQL\ResolverRegistry $registry
   * @param \Drupal\graphql\GraphQL\ResolverBuilder $builder
   */
  protected function addQueryFields(ResolverRegistry $registry, ResolverBuilder $builder) {
    $registry->addFieldResolver('Query', 'menu',
      $builder->produce('entity_load')
        ->map('type', $builder->fromValue('node'))
        ->map('bundles', $builder->fromValue(['menu']))
        ->map('id', $builder->fromArgument('id'))
    );
  }

  /**
   * @param \Drupal\graphql\GraphQL\ResolverRegistry $registry
   * @param \Drupal\graphql\GraphQL\ResolverBuilder $builder
   */
  protected function addMenuFields(ResolverRegistry $registry, ResolverBuilder $builder) {
    $registry->addFieldResolver('Menu', 'id',
      $builder->produce('entity_id')
        ->map('entity', $builder->fromParent())
    );

    $registry->addFieldResolver('Menu', 'title',
      $builder->produce('entity_label')
        ->map('entity', $builder->fromParent())
    );

    $registry->addFieldResolver('Menu', 'groups',
      $builder->produce('entity_reference')
        ->map('entity', $builder->fromParent())
        ->map('field', $builder->fromValue('field_menu_group'))
    );
  }

  /**
  * @param \Drupal\graphql\GraphQL\ResolverRegistry $registry
  * @param \Drupal\graphql\GraphQL\ResolverBuilder $builder
  */
  protected function addGroupFields(ResolverRegistry $registry, ResolverBuilder $builder) {
    $registry->addFieldResolver('Group', 'name',
      $builder->callback(function ($entity) {
        return $entity->field_name->value;
      })
    );

    $registry->addFieldResolver('Group', 'items',
      $builder->produce('entity_reference')
        ->map('entity', $builder->fromParent())
        ->map('field', $builder->fromValue('field_item'))
    );
  }

  /**
  * @param \Drupal\graphql\GraphQL\ResolverRegistry $registry
  * @param \Drupal\graphql\GraphQL\ResolverBuilder $builder
  */
  protected function addItemFields(ResolverRegistry $registry, ResolverBuilder $builder) {
    $registry->addFieldResolver('Item', 'name',
      $builder->callback(function ($entity) {
        return $entity->field_name->value;
      })
    );

    $registry->addFieldResolver('Item', 'description',
      $builder->callback(function ($entity) {
        return $entity->field_description->value;
      })
    );

    $registry->addFieldResolver('Item', 'price',
      $builder->callback(function ($entity) {
        return $entity->field_price->value;
      })
    );

    $registry->addFieldResolver('Item', 'image', $builder->compose(
      $builder->callback(function ($entity) {
        return $entity->field_img->entity;
      }),
      $builder->callback(function ($entity) {
        $uri = $entity->getFileUri();
        return [
          file_create_url($uri),
          ImageStyle::load('menu')->buildUrl($uri)
        ];
      })
    ));
  }

}
