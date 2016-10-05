<?php

/**
 * @file
 * Contains \Drupal\shariff_backend\ShariffBackendInterface.
 */

namespace Drupal\shariff_backend;

/**
 * Interface for Shariff backend service classes.
 */
interface ShariffBackendInterface {

  /**
   * Return share counts.
   *
   * @param string $url
   *   The URL to get the share counts for.
   * @param boolean $refresh
   *   Get uncached counts if set to <code>TRUE</code>.
   *
   * @return array|null
   *   An array of counts by service on success, otherwise NULL. The array has
   *   the following keys (depending on the configured services):
   *     - addthis
   *     - facebook
   *     - flattr
   *     - googleplus
   *     - linkedin
   *     - pinterest
   *     - reddit
   *     - stumbleupon
   */
  public function getCounts($url, $refresh = FALSE);

  /**
   * Return multiple share counts.
   *
   * @param array $url
   *   A of URLs to get the share counts for.
   *
   * @return array|null
   *   A array of counts by URL on success, otherwise NULL. Each item is an
   *   array with the following keys (depending on the configured services):
   *     - addthis
   *     - facebook
   *     - flattr
   *     - googleplus
   *     - linkedin
   *     - pinterest
   *     - reddit
   *     - stumbleupon
   */
  public function getCountsMultiple(array $urls);

  /**
   * Return backend options.
   *
   * @return ShariffBackendOptionsInterface
   *   The Shariff backend options object.
   */
  public function getOptions();

}
