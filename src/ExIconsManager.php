<?php

namespace Drupal\ex_icons;

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
class ExIconsManager extends DefaultPluginManager implements ExIconsManagerInterface {

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
    // Default class for icon implementations.
    'class' => 'Drupal\ex_icons\ExIcon',
  ];

  /**
   * The relative, printable URLS for sprite sheets per provider.
   *
   * @var string[]
   */
  protected $providerUrls = [];

  /**
   * The inline defs markup keyed by provider.
   *
   * @var string[]
   */
  protected $inlineDefs = [];

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
  public function buildUrl($id) {
    $plugin = $this->getDefinition($id);
    $provider = $plugin['provider'];

    if (!isset($this->providerUrls[$provider])) {
      if ($cache = $this->cacheBackend->get("$this->cacheKey:$provider")) {
        $this->providerUrls[$provider] = $cache->data;
      }
      else {
        // Get the provider's icons.
        $icons = [];
        foreach ($this->getDefinitions() as $plugin_id => $plugin_definition) {
          if ($plugin_definition['provider'] == $provider) {
            $icons[$plugin_id] = $plugin_definition;
          }
        }
        // Sort to ensure that changes in order do not change generated hash.
        ksort($icons, SORT_STRING);

        // Get any inline def content.
        $inline_defs = $this->getInlineDefs()[$provider];

        // Resolve provider path, first assuming module then fallback to theme.
        $provider_path = $this->moduleHandler->moduleExists($provider)
          ? $this->moduleHandler->getModule($provider)->getPath()
          : $this->themeHandler->themeExists($provider)
          ? $this->themeHandler->getTheme($provider)->getPath()
          : '';

        // Construct the relative URL, with version hash from icon definitions
        // and inline defs.
        $url = file_url_transform_relative(file_create_url("$provider_path/" . self::BASENAME . '.svg'))
          . '?'
          . substr(hash('sha512', json_encode($icons) . $inline_defs), 0, 16);

        $this->cacheBackend->set(
          "$this->cacheKey:$provider",
          $url,
          CacheBackendInterface::CACHE_PERMANENT,
          ['ex_icons']
        );

        $this->providerUrls[$provider] = $url;
      }
    }

    return $this->providerUrls[$provider] . "#$id";
  }

  /**
   * {@inheritdoc}
   */
  public function clearCachedDefinitions() {
    parent::clearCachedDefinitions();
    $this->providerUrls = [];
    $this->inlineDefs = [];
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

}
