<?php

namespace Drupal\Tests\ex_icons\Unit;

use Drupal\Component\FileCache\FileCacheFactory;
use Drupal\Tests\UnitTestCase;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamWrapper;

/**
 * @coversDefaultClass \Drupal\ex_icons\Discovery\SvgSymbolDiscovery
 * @group ex_icons
 */
class SvgSymbolDiscoveryTest extends UnitTestCase {

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    // Ensure that FileCacheFactory has a prefix.
    FileCacheFactory::setPrefix('prefix');
  }

  /**
   * Tests the SVG symbol discovery.
   */
  public function testDiscovery() {
    vfsStreamWrapper::register();
    $root = new vfsStreamDirectory('modules');
    vfsStreamWrapper::setRoot($root);
    $url = vfsStream::url('modules');

    mkdir("$url/test_1/test", 0777, TRUE);
    copy(__DIR__ . '/../../fixtures/sprites.svg', "$url/test_1/test/sprites.svg");

    // Directory with no icon sheet.
    mkdir("$url/test_2");

    // Set up the directories to search.
    $directories = [
      'test_1' => "$url/test_1",
      'test_2' => "$url/test_2",
    ];

    $discovery = new TestSvgSymbolDiscovery('test/sprites', $directories);
    $data = $discovery->findAll();

    $this->assertEquals(1, count($data));
    $this->assertArrayHasKey('test_1', $data);

    $this->assertArrayHasKey('icons', $data['test_1']);
    $this->assertArrayHasKey('icon', $data['test_1']['icons']);
    $this->assertArrayHasKey('icon-no-title', $data['test_1']['icons']);

    $this->assertArrayHasKey('inline_defs', $data['test_1']);
    $this->assertNotEmpty($data['test_1']['inline_defs']);

    $this->assertArrayHasKey('base_url', $data['test_1']);
    $this->assertRegExp(
      "@^transformed:$url/test_1/test/sprites\\.svg\\?.+$@",
      $data['test_1']['base_url']
    );
  }

}
