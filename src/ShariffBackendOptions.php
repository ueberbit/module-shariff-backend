<?php

/**
 * @file
 * Contains \Drupal\shariff_backend\ShariffBackendOptions.
 */

namespace Drupal\shariff_backend;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Site\Settings;
use Drupal\Core\Url;

/**
 * Shariff backend options service class.
 *
 * See https://github.com/heiseonline/shariff-backend-php for available options.
 */
class ShariffBackendOptions implements ShariffBackendOptionsInterface {

  /**
   * A configuration factory object.
   *
   * @var ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Backend options.
   *
   * @var array
   */
  protected $options = [];

  /**
   * ShariffBackendOptions constructor.
   *
   * @param ConfigFactoryInterface $config_factory
   *  A configuration factory object.
   */
  public function __construct(ConfigFactoryInterface $config_factory) {
    $this->configFactory = $config_factory;

    // Load configuration.
    $config = $this->configFactory->get('shariff_backend.settings');

    // Build options.
    $this->options = [
      'cacheClass' => "Drupal\\shariff_backend\\ShariffBackendCache",
      'cache' => [
        'ttl' => $this->getCacheMaxAge(),
      ],
      'client' => [
        'timeout' => 1.5,
        'connect_timeout' => 1.5,
      ],
      'domains' => $this->getDomains(),
      'services' => $this->getServices()
    ];

    // Inject Facebook app credentials (if configured).
    if (($fb_app_id = $config->get('facebook_app_id')) && ($fb_app_secret = $config->get('facebook_app_secret'))) {
      $this->options[static::SERVICE_FACEBOOK] = [
        'app_id' => $fb_app_id,
        'secret' => $fb_app_secret,
      ];
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheContexts() {
    return ['url'];
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheTags() {
    return ['shariff_backend_count'];
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheMaxAge() {
    $config = $this->configFactory->get('shariff_backend.settings');
    $ttl = $config->get('cache_ttl');

    return $ttl ? $ttl : 0;
  }

  /**
   * {@inheritdoc}
   */
  public function getDomains() {
    $domains = [
      \Drupal::service('router.request_context')->getCompleteBaseUrl(),
    ];

    if ($base_domain = $this->configFactory->get('shariff_backend.settings')->get('base_domain')) {
      $domains[] = $base_domain;
    }

    // Allow adding domains via settings.php.
    $backend_settings = Settings::get('shariff_backend');
    $domains_additional = empty($backend_settings['domains']) ? [] : $backend_settings['domains'];
    if (is_array($domains_additional)) {
      $domains = array_merge($domains, $domains_additional);
    }

    return array_map(function ($domain) {
      return parse_url($domain, PHP_URL_HOST);
    }, $domains);
  }

  /**
   * {@inheritdoc}
   */
  public function getServices() {
    $config = $this->configFactory->get('shariff_backend.settings');
    $services = $config->get('services');
    return $services;
  }

  /**
   * {@inheritdoc}
   */
  public function toArray() {
    return $this->options;
  }

}
