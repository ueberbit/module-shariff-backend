<?php

namespace Drupal\shariff_backend\Plugin\prepared_data\Processor;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\prepared_data\PreparedDataInterface;
use Drupal\prepared_data\Processor\ProcessorBase;
use Drupal\shariff_backend\ShariffBackendInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * ShariffSocialCounts processor class.
 *
 * @PreparedDataProcessor(
 *   id = "shariff",
 *   label = @Translation("Social counts provided by Shariff Backend"),
 *   weight = 20,
 *   manageable = true
 * )
 */
class ShariffSocialCounts extends ProcessorBase implements ContainerFactoryPluginInterface {

  /**
   * The shariff backend service.
   *
   * @var \Drupal\shariff_backend\ShariffBackendInterface
   */
  protected $shariffBackend;

  /**
   * Constructs ShariffSocialCounts object.
   *
   * @param \Drupal\shariff_backend\ShariffBackendInterface $shariff_backend
   *   The shariff backend service.
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   */
  public function __construct(ShariffBackendInterface $shariff_backend, array $configuration, $plugin_id, $plugin_definition) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->shariffBackend = $shariff_backend;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    /** @var \Drupal\shariff_backend\ShariffBackendInterface $shariff_backend */
    $shariff_backend = $container->get('shariff_backend.backend');
    return new static($shariff_backend, $configuration, $plugin_id, $plugin_definition);
  }

  /**
   * {@inheritdoc}
   */
  public function process(PreparedDataInterface $data) {
    $info = $data->info();
    if (empty($info['entity']) || !($info['entity'] instanceof EntityInterface)) {
      $data_array = &$data->data();
      unset($data_array['shariff']);
      return;
    }
    /** @var \Drupal\Core\Entity\EntityInterface $entity */
    $entity = $info['entity'];
    try {
      $url = $entity->toUrl('canonical')->setAbsolute()->toString();
      $counts = $this->shariffBackend->getCounts($url, TRUE);
    }
    catch (\Exception $e) {
      $data->data()['shariff'] = new \stdClass();
      return;
    }

    foreach ($counts as $platform => $result) {
      if (empty($result['raw'])) {
        // Skip zero-values.
        unset($counts[$platform]);
      }
    }

    $data->data()['shariff'] = !empty($counts) ? $counts : new \stdClass();
  }

  /**
   * {@inheritdoc}
   */
  public function cleanup(PreparedDataInterface $data) {
    if (!$this->isEnabled()) {
      $data_array = &$data->data();
      unset($data_array['shariff']);
    }
  }

}
