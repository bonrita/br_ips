<?php

namespace Drupal\br_ips;

use Drupal\br_menu\BrMenuLink;
use Drupal\br_site_entry\SiteEntryManagerInterface;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\Entity\Query\QueryFactory;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\domain\DomainNegotiatorInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Drupal\Core\Cache\CacheableMetadata;

/**
 * Industry Manager service.
 *
 * @package Drupal\br_ips
 */
class IndustryManager implements IndustryManagerInterface {

  /**
   * @var \Drupal\Core\Entity\Query\QueryFactory
   */
  protected $entityQuery;

  /**
   * @var \Drupal\Core\Entity\EntityTypeManager
   */
  protected $entityTypeManager;

  /**
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected $routeMatch;

  /**
   * The current request.
   *
   * @var \Symfony\Component\HttpFoundation\Request
   */
  protected $currentRequest;

  /**
   * @var string
   */
  protected $currentLanguageCode;

  /**
   * The industry the visitor prefers.
   *
   * @var string
   */
  protected $preferredIndustry = '';

  /**
   * Constructor.
   */
  public function __construct(QueryFactory $entity_query, EntityTypeManager $entity_type_manager, RouteMatchInterface $route_match, RequestStack $request_stack, LanguageManagerInterface $languageManager) {
    $this->entityQuery = $entity_query;
    $this->entityTypeManager = $entity_type_manager;
    $this->routeMatch = $route_match;
    $this->currentRequest = $request_stack->getCurrentRequest();
    $this->currentLanguageCode = $languageManager->getCurrentLanguage()
      ->getId();
  }

  /**
   * {@inheritdoc}
   */
  public function getPreferredIndustry() {
    $industry = $this->preferredIndustry;;

    // The first time this service is called, the preferred industry is taken
    // from the cookie. When the cookie is changed by ::setPreferredIndustry()
    // the new value will be used and the old cookie value is ignored.
    if (empty($industry)) {
      $cookies = $this->currentRequest->cookies;
      if ($cookies->has('industry')) {
        $industry = $cookies->get('industry');
        $this->preferredIndustry = $industry;
      }
    }

    return $industry;
  }

  /**
   * {@inheritdoc}
   */
  public function setPreferredIndustry($industry) {

    $params = session_get_cookie_params();
    setcookie('industry', $industry, REQUEST_TIME + SiteEntryManagerInterface::COOKIE_LIFETIME, $params['path'], $params['domain'], $params['secure'], $params['httponly']);

    // Store the cookie value we have just set to re-use is later during this
    // page call.
    $this->preferredIndustry = $industry;
  }

  /**
   * {@inheritdoc}
   */
  public function getCurrentTermId() {

    return $this->getPreferredIndustry();
  }

  /**
   * {@inheritdoc}
   */
  public function getCurrentTerm() {

    /** @var \Drupal\taxonomy\Entity\Term $entity */
    $entity = NULL;

    $tid = $this->getCurrentTermId();
    if ($tid) {
      /** @var \Drupal\taxonomy\Entity\Term $entity */
      $entity = $this->entityTypeManager
        ->getStorage('taxonomy_term')
        ->load($tid);
      if ($entity && $entity->hasTranslation($this->currentLanguageCode)) {
        $entity = $entity->getTranslation($this->currentLanguageCode);
      }
    }

    return $entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getCurrentNode() {
    static $node = NULL;

    if (empty($node)) {
      $tid = $this->getCurrentTermId();
      $nids = $this->entityQuery->get('node')
        ->condition('status', 1)
        ->condition('type', 'industry')
        ->condition($this::TERM_INDUSTRY_FIELD, $tid)
        ->range(0, 1)
        ->addTag('node_access')
        ->execute();

      if ($nids) {
        /** @var \Drupal\node\Entity\Node $node */
        $node = $this->entityTypeManager->getStorage('node')
          ->load(reset($nids));
        if ($node->hasTranslation($this->currentLanguageCode)) {
          $node = $node->getTranslation($this->currentLanguageCode);
        }
      }
    }

    return $node;
  }

  /**
   * {@inheritdoc}
   */
  public function getCountryIndustries() {
    $nodes = [];

    // Get Industry nodes that match the terms and order them by term weight.
    $nids = $this->entityQuery->get('node')
      ->condition('status', 1)
      ->condition('type', 'industry')
      ->addTag('node_access')
      ->sort('br_menu_link.entity.weight', 'ASC')
      ->sort('br_menu_link.entity.title', 'ASC')
      ->execute();

    if ($nids) {
      /** @var \Drupal\node\Entity\Node[] $nodes */
      $nodes = $this->entityTypeManager->getStorage('node')
        ->loadMultiple($nids);
      foreach ($nodes as $key => $node) {
        if ($node->hasTranslation($this->currentLanguageCode)) {
          $entities[$key] = $node->getTranslation($this->currentLanguageCode);
        }
      }
    }

    return $nodes;
  }

  /**
   * {@inheritdoc}
   */
  // @todo Add caching if this proves to be an expensive operation.
  public function menuTermOptionList() {
    $list = [];
    $tids = [];

    // Load all Industry terms.
    /** @var \Drupal\taxonomy\Entity\Term[] $terms */
    $terms = $this->entityTypeManager->getStorage('taxonomy_term')
      ->loadTree($this::INDUSTRY_VOCABULARY, 0, 1, TRUE);
    foreach ($terms as $term) {
      $tids[] = $term->id();
    }

    // Load the nodes that use these Industry terms (limited by
    // Industry content types and domain).
    $nids = $this->entityQuery->get('node')
      ->condition('type', $this::INDUSTRY_CONTENT_TYPE)
      ->condition('status', 1)
      ->condition($this::TERM_INDUSTRY_FIELD, $tids, 'IN')
      ->addTag('node_access')
      ->sort('br_menu_link.entity.weight', 'ASC')
      ->sort('br_menu_link.entity.title', 'ASC')
      ->execute();

    // Build a list terms keyed by tid and with the menu link title as value.
    /** @var \Drupal\node\Entity\Node[] $nodes */
    $nodes = $this->entityTypeManager->getStorage('node')->loadMultiple($nids);
    foreach ($nodes as $node) {
      $tid = $node->{$this::TERM_INDUSTRY_FIELD}->target_id;
      $list[$tid] = BrMenuLink::getTitle($node);
    }

    return $list;
  }

  /**
   * Get the current industry id when accessed via a menu.
   *
   * @param null|\Drupal\Core\Cache\CacheableMetadata $cache
   *   The cache instance.
   *
   * @return int|null
   *   The industry id.
   */
  public function getCurrentMenuHitIndustryid($cache = NULL) {
    $industry_id = NULL;

    if ($this->routeMatch->getRouteName() == 'entity.node.canonical') {
      /** @var \Symfony\Component\HttpFoundation\ParameterBag $parameters */
      $parameters = $this->routeMatch->getParameters();
      /** @var \Drupal\node\Entity\Node $node */
      $node = $parameters->get('node');

      if ($node->bundle() == 'industry') {
        /** @var \Drupal\taxonomy\Entity\Term $term */
        $term = $node->field__term_industry->referencedEntities()[0];

        $industry_id = $term->id();
        if ($cache instanceof CacheableMetadata) {
          $cache->addCacheableDependency($term);
          $cache->addCacheableDependency($node);
        }
      }
    }

    return $industry_id;
  }

}
