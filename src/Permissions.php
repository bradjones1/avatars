<?php

/**
 * @file
 * Contains \Drupal\ak\Permissions.
 */

namespace Drupal\ak;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Define a permission generator.
 */
class Permissions implements ContainerInjectionInterface {

  use StringTranslationTrait;

  /**
   * The avatar generator plugin manager.
   *
   * @var \Drupal\ak\AvatarGeneratorPluginManagerInterface
   */
  protected $avatarGenerator;

  /**
   * Constructs the permission generator.
   *
   * @param \Drupal\ak\AvatarGeneratorPluginManagerInterface $avatar_generator
   *   The avatar generator plugin manager.
   */
  public function __construct(AvatarGeneratorPluginManagerInterface $avatar_generator) {
    $this->avatarGenerator = $avatar_generator;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('plugin.manager.avatar_generator')
    );
  }

  /**
   * Define permissions for picture providers.
   *
   * @return array
   */
  public function avatarGenerators() {
    $permissions = [];

    foreach ($this->avatarGenerator->getDefinitions() as $plugin_id => $definition) {
      if ($plugin_id == 'broken') {
        continue;
      }

      $t_args = [
        '%avatar_generator' => $definition['label'],
        '%provider' => $definition['provider'],
      ];

      $permissions["ak avatar_generator user $plugin_id"] = [
        'title' => $this->t('Use %avatar_generator by %provider', $t_args),
        'description' => $this->t('User can select %avatar_generator avatar generator.', $t_args),
      ];
    }

    return $permissions;
  }

}
