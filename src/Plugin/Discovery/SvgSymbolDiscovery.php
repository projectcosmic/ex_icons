<?php

namespace Drupal\ex_icons\Plugin\Discovery;

use Drupal\Component\Plugin\Discovery\DiscoveryInterface;
use Drupal\Component\Plugin\Discovery\DiscoveryTrait;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\ex_icons\Discovery\SvgSymbolDiscovery as MainSvgSymbolDiscovery;

/**
 * Allows SVG files to define plugin definitions.
 *
 * If the value of a key (like title) in the definition is translatable then
 * the addTranslatableProperty() method can be used to mark it as such and also
 * to add translation context. Then
 * \Drupal\Core\StringTranslation\TranslatableMarkup will be used to translate
 * the string and also to mark it safe.
 */
class SvgSymbolDiscovery implements DiscoveryInterface {

  use DiscoveryTrait;

  /**
   * SVG file discovery and parsing handler.
   *
   * @var \Drupal\ex_icons\Discovery\SvgSymbolDiscovery
   */
  protected $discovery;

  /**
   * Contains an array of translatable properties passed along to t().
   *
   * @var array
   *
   * @see \Drupal\ex_icons\Plugin\Discovery\SvgSymbolDiscovery::addTranslatableProperty()
   */
  protected $translatableProperties = [];

  /**
   * Construct a SvgSymbolDiscovery object.
   *
   * @param string $basename
   *   The basename of the file to use for discovery; for example, 'foo/bar'
   *   will become 'foo/bar.svg'.
   * @param array $directories
   *   An array of directories to scan.
   */
  public function __construct($basename, array $directories) {
    $this->discovery = new MainSvgSymbolDiscovery($basename, $directories);
  }

  /**
   * Set one of the property values as being translatable.
   *
   * @param string $value_key
   *   The key corresponding to a value in the SVG result that contains a
   *   translatable string.
   * @param string $context_key
   *   (Optional) the translation context for the value specified by the
   *   $value_key.
   *
   * @return $this
   */
  public function addTranslatableProperty($value_key, $context_key = '') {
    $this->translatableProperties[$value_key] = $context_key;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getDefinitions() {
    $sheets = $this->discovery->findAll();

    // Flatten definitions into what's expected from plugins.
    $definitions = [];
    foreach ($sheets as $provider => $data) {
      foreach ($data['icons'] as $id => $definition) {
        // Add TranslatableMarkup.
        foreach ($this->translatableProperties as $property => $context_key) {
          if (isset($definition[$property])) {
            $options = [];
            // Move the t() context from the definition to the translation
            // wrapper.
            if ($context_key && isset($definition[$context_key])) {
              $options['context'] = $definition[$context_key];
              unset($definition[$context_key]);
            }
            $definition[$property] = new TranslatableMarkup($definition[$property], [], $options);
          }
        }
        // Add ID and provider.
        $definitions["$provider/$id"] = $definition + [
          'provider' => $provider,
          'id' => $id,
        ];
      }
    }

    return $definitions;
  }

}
