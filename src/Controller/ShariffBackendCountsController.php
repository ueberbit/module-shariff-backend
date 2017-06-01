<?php

/**
 * @file
 * Contains Drupal\shariff_backend\Controller\ShariffBackendCountsController.
 */

namespace Drupal\shariff_backend\Controller;

use Drupal\Component\Utility\UrlHelper;
use Drupal\Component\Utility\Xss;
use Drupal\shariff_backend\ShariffBackendInterface;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class ShariffCountsController.
 */
class ShariffBackendCountsController extends ControllerBase {

  /**
   * A Shariff backend srvice instance.
   *
   * @var ShariffBackendInterface
   */
  protected $backend;

  /**
   * Constructs a ShariffCountsController object.
   *
   * @param ShariffBackendInterface $backend
   *   The Shariff backend service.
   */
  public function __construct(ShariffBackendInterface $backend) {
    $this->backend = $backend;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('shariff_backend.backend')
    );
  }

  /**
   * Build JSON response with share counts for passed URL.
   *
   * @return \Drupal\Core\Cache\CacheableJsonResponse
   *   The response object.
   */
  public function counts() {
    $result = [];

    // URL has been provided?
    if (empty($_REQUEST['url'])) {
      throw new NotFoundHttpException();
    }

    $urls = is_array($_REQUEST['url']) ? $_REQUEST['url'] : [$_REQUEST['url']];
    $refresh = isset($_REQUEST['refresh']);

    foreach ($urls as $url) {
      // Filter URL.
      $url = Xss::filter($url);

      // Is a valid URL?
      if (!UrlHelper::isValid($url, TRUE)) {
        throw new NotFoundHttpException();
      }

      // Retrieve counts.
      $counts = $this->backend->getCounts($url, $refresh);

      // Build JSON.
      $json = [
        'url' => $url,
        'error' => FALSE,
      ];

      // Counts have been retrieved?
      if ($counts) {
        $json['counts'] = $counts;
      }

      // No counts available -> error.
      else {
        $json['error'] = TRUE;
      }

      $result[] = $json;
    }

    // Build response.
    $response = new JsonResponse();
    $response->setData($result);

    // Set cache max-age to 1 day.
    $max_age = 86400;
    $response->setPublic();
    $response->setMaxAge($max_age);

    $expires = new \DateTime();
    $expires->setTimestamp(REQUEST_TIME + $max_age);
    $response->setExpires($expires);

    return $response;
  }

}
