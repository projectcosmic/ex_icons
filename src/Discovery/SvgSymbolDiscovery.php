<?php

namespace Drupal\ex_icons\Discovery;

use Drupal\Component\Discovery\DiscoverableInterface;
use Drupal\Component\FileCache\FileCacheFactory;
use Drupal\ex_icons\Serialization\SvgSpriteSheet;

/**
 * Provides discovery for SVG symbol icons within a given set of directories.
 */
class SvgSymbolDiscovery implements DiscoverableInterface {

  /**
   * An array of directories to scan, keyed by the provider.
   *
   * @var array
   */
  protected $directories = [];

  /**
   * Constructs a SvgSymbolDiscovery object.
   *
   * @param string $basename
   *   The basename of the file to look for in each directory. Can include
   *   slashes to designate sub-directories.
   * @param array $directories
   *   An array of directories to scan, keyed by the provider.
   */
  public function __construct($basename, array $directories) {
    $this->basename = $basename;
    $this->directories = $directories;
  }

  /**
   * {@inheritdoc}
   */
  public function findAll() {
    $all = [];

    $files = $this->findFiles();
    $provider_by_files = array_flip($files);

    $file_cache = FileCacheFactory::get('svg_symbol_discovery');

    // Try to load from the file cache first.
    foreach ($file_cache->getMultiple($files) as $file => $data) {
      $all[$provider_by_files[$file]] = $data;
      unset($provider_by_files[$file]);
    }

    // If there are files left that were not returned from the cache, load and
    // parse them now. This list was flipped above and is keyed by filename.
    if ($provider_by_files) {
      foreach ($provider_by_files as $file => $provider) {
        $data = $this->decode($file);
        $all[$provider] = [
          'base_url' => $this->transformFileUrl($file)
          . '?'
          . substr(hash('sha512', json_encode($data)), 0, 16),
        ] + $data;
        $file_cache->set($file, $all[$provider]);
      }
    }

    return $all;
  }

  /**
   * Decode a SVG sprite sheet file.
   *
   * @param string $file
   *   SVG file path.
   *
   * @return array
   *   SVG sprite sheet data.
   */
  protected function decode($file) {
    // If a file is empty or its contents are commented out, return an empty
    // array instead of NULL for type consistency.
    return SvgSpriteSheet::decode(file_get_contents($file)) ?: [];
  }

  /**
   * Returns an array of file paths, keyed by provider.
   *
   * @return string[]
   *   The list of file paths, keyed by provider.
   */
  protected function findFiles() {
    $files = [];

    foreach ($this->directories as $provider => $directory) {
      $file = "$directory/$this->basename.svg";
      if (file_exists($file)) {
        $files[$provider] = $file;
      }
    }

    return $files;
  }

  /**
   * Transforms a file path to a relative, web-accessible URL.
   *
   * @param string $file_path
   *   The path to transform.
   *
   * @return string
   *   The transformed, web-accessible URL.
   */
  protected function transformFileUrl($file_path) {
    $relative = str_replace(\Drupal::service('app.root'), '', $file_path);
    return file_url_transform_relative(file_create_url($relative));
  }

}
