<?php

namespace Drupal\ex_icons_menu\Menu;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Menu\MenuLinkTreeInterface;
use Drupal\Core\Menu\MenuTreeParameters;

/**
 * Wraps menu link tree service to add icon data to menu items.
 */
class MenuLinkTree implements MenuLinkTreeInterface {

  /**
   * The original menu link tree service.
   *
   * @var \Drupal\Core\Menu\MenuLinkTreeInterface
   */
  protected $inner;

  /**
   * The menu link storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $menuLinkContentStorage;

  /**
   * Constructs a MenuLinkTree object.
   *
   * @param \Drupal\Core\Menu\MenuLinkTreeInterface $inner
   *   The original menu link tree service.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(MenuLinkTreeInterface $inner, EntityTypeManagerInterface $entity_type_manager) {
    $this->inner = $inner;
    $this->menuLinkContentStorage = $entity_type_manager->getStorage('menu_link_content');
  }

  /**
   * {@inheritdoc}
   */
  public function getCurrentRouteMenuTreeParameters($menu_name) {
    return $this->inner->getCurrentRouteMenuTreeParameters($menu_name);
  }

  /**
   * {@inheritdoc}
   */
  public function load($menu_name, MenuTreeParameters $parameters) {
    return $this->inner->load($menu_name, $parameters);
  }

  /**
   * {@inheritdoc}
   */
  public function transform(array $tree, array $manipulators) {
    return $this->inner->transform($tree, $manipulators);
  }

  /**
   * {@inheritdoc}
   */
  public function build(array $tree) {
    $build = $this->inner->build($tree);

    if (isset($build['#items'])) {
      $this->setIcons($build['#items']);
    }

    return $build;
  }

  /**
   * Sets icon data on to a list of menu links.
   *
   * @param \Drupal\Core\Menu\MenuLinkInterface[] &$items
   *   The list of menu link items.
   */
  protected function setIcons(array &$items) {
    foreach ($items as $id => $item) {
      $link = $item['original_link'];
      $meta = $link->getMetadata();

      $icon = '';

      if (isset($meta['ex_icons_menu_icon'])) {
        $icon = $meta['ex_icons_menu_icon'];
      }
      elseif (isset($meta['entity_id'])) {
        /** @var \Drupal\menu_link_content\Entity\MenuLinkContentInterface|null $entity */
        $entity = $this->menuLinkContentStorage->load($meta['entity_id']);
        if ($entity) {
          $icon = $entity->ex_icons_menu_icon->value;
        }
      }

      $items[$id]['icon'] = $icon;

      if ($item['below']) {
        $this->setIcons($items[$id]['below']);
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function maxDepth() {
    return $this->inner->maxDepth();
  }

  /**
   * {@inheritdoc}
   */
  public function getSubtreeHeight($id) {
    return $this->inner->getSubtreeHeight($id);
  }

  /**
   * {@inheritdoc}
   */
  public function getExpanded($menu_name, array $parents) {
    return $this->inner->getExpanded($menu_name, $parents);
  }

}
