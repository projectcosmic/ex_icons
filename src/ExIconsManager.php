<?php

namespace Drupal\ex_icons;

use Drupal\Component\Plugin\FallbackPluginManagerInterface;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Extension\ThemeHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\Core\Plugin\Discovery\ContainerDerivativeDiscoveryDecorator;
use Drupal\Core\Plugin\Factory\ContainerFactory;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\StringTranslation\TranslationInterface;
use Drupal\ex_icons\Plugin\Discovery\SvgSymbolDiscovery;

/**
 * Defines a plugin manager to deal with external-use icons.
 */
class ExIconsManager extends DefaultPluginManager implements ExIconsManagerInterface, FallbackPluginManagerInterface {

  use StringTranslationTrait;

  /**
   * The basename of sprite sheets to discover within extensions.
   */
  const BASENAME = 'dist/icons';

  /**
   * {@inheritdoc}
   */
  protected $defaults = [
    // Human readable label for icon.
    'label' => '',
    // The plugin id. Set by the plugin system based on the symbol ID attribute.
    'id' => '',
    // Width of the icon.
    'width' => 1,
    // Height of the icon.
    'height' => 1,
    // URL of the icon.
    'url' => '',
    // Default class for icon implementations.
    'class' => 'Drupal\ex_icons\ExIcon',
  ];

  /**
   * The inline defs markup keyed by provider.
   *
   * @var string[]|null
   */
  protected $inlineDefs;

  /**
   * Instantiated plugin instances.
   *
   * @var \Drupal\ex_icons\ExIconInterface[]
   */
  protected $instances = [];

  /**
   * The object that discovers plugins managed by this manager.
   *
   * @var \Drupal\ex_icons\Plugin\Discovery\SvgSymbolDiscoveryInterface
   */
  protected $discovery;

  /**
   * The theme handler.
   *
   * @var \Drupal\Core\Extension\ThemeHandlerInterface
   */
  protected $themeHandler;

  /**
   * Constructs a new ExIconsManager instance.
   *
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler.
   * @param \Drupal\Core\Extension\ThemeHandlerInterface $theme_handler
   *   The theme handler.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   The cache backend.
   * @param \Drupal\Core\StringTranslation\TranslationInterface $string_translation
   *   The string translation service.
   */
  public function __construct(ModuleHandlerInterface $module_handler, ThemeHandlerInterface $theme_handler, CacheBackendInterface $cache_backend, TranslationInterface $string_translation) {
    $this->factory = new ContainerFactory($this);
    $this->moduleHandler = $module_handler;
    $this->themeHandler = $theme_handler;
    $this->setStringTranslation($string_translation);
    $this->alterInfo('ex_icons');
    $this->setCacheBackend($cache_backend, 'ex_icons', ['ex_icons']);
  }

  /**
   * Gets the plugin discovery.
   *
   * @return \Drupal\ex_icons\Plugin\Discovery\SvgSymbolDiscoveryInterface
   *   The plugin discovery.
   */
  protected function getDiscovery() {
    if (!isset($this->discovery)) {
      $this->discovery = new SvgSymbolDiscovery(
        self::BASENAME,
        $this->moduleHandler->getModuleDirectories()
        + $this->themeHandler->getThemeDirectories()
      );
      $this->discovery->addTranslatableProperty('label');
      $this->discovery = new ContainerDerivativeDiscoveryDecorator($this->discovery);
    }

    return $this->discovery;
  }

  /**
   * {@inheritdoc}
   */
  protected function providerExists($provider) {
    return $this->moduleHandler->moduleExists($provider) || $this->themeHandler->themeExists($provider);
  }

  /**
   * {@inheritdoc}
   */
  public function clearCachedDefinitions() {
    parent::clearCachedDefinitions();
    $this->inlineDefs = NULL;
    $this->instances = [];
  }

  /**
   * {@inheritdoc}
   */
  public function getInlineDefs() {
    if (!isset($this->inlineDefs)) {
      $this->inlineDefs = $this->getDiscovery()->getInlineDefs();
    }

    return $this->inlineDefs;
  }

  /**
   * {@inheritdoc}
   */
  public function getInstance(array $options) {
    if (isset($options['id'])) {
      $id = $options['id'];

      if (!isset($this->instances[$id])) {
        $this->instances[$id] = $this->createInstance($id);
      }

      return $this->instances[$id];
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getFallbackPluginId($plugin_id, array $configuration = []) {
    return 'ex_icon_null';
  }

}
