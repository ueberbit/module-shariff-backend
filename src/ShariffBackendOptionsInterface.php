<?php

/**
 * @file
 * Contains \Drupal\shariff_backend\ShariffBackendOptionsInterface.
 */

namespace Drupal\shariff_backend;

use Drupal\Core\Cache\CacheableDependencyInterface;

/**
 * Interface for Shariff backend options service classes.
 */
interface ShariffBackendOptionsInterface extends CacheableDependencyInterface {

  /**
   * Supported service: AddThis.
   */
  const SERVICE_ADDTHIS = 'AddThis';

  /**
   * Supported service: Facebook.
   */
  const SERVICE_FACEBOOK = 'Facebook';

  /**
   * Supported service: FLattr.
   */
  const SERVICE_FLATTR = 'Flattr';

  /**
   * Supported service: Google Plus.
   */
  const SERVICE_GOOGLE_PLUS = 'GooglePlus';

  /**
   * Supported service: LinkedIn.
   */
  const SERVICE_LINKEDIN = 'LinkedIn';

  /**
   * Supported service: Pinterest.
   */
  const SERVICE_PINTEREST = 'Pinterest';

  /**
   * Supported service: Reddit.
   */
  const SERVICE_REDDIT = 'Reddit';

  /**
   * Supported service: StumbleUpon.
   */
  const SERVICE_STUMBLEUPON = 'StumbleUpon';

  /**
   * Supported service: Xing.
   */
  const SERVICE_XING = 'Xing';

  /**
   * Return domain names to take into account when requesting counts.
   *
   * @return array
   *   An array of domain names.
   */
  public function getDomains();

  /**
   * Return names of requested services.
   *
   * @return array
   *   An array of service name constant values.
   */
  public function getServices();

  /**
   * Return options as array.
   *
   * @return array
   *   A keyed options array to pass to a Shariff backend instance.
   */
  public function toArray();

}
