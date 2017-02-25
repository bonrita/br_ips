<?php

namespace Drupal\br_ips;

use Drupal\Core\Entity\Query\QueryFactory;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\domain\DomainNegotiatorInterface;

/**
 * Solution Category manager.
 *
 * @package Drupal\br_ips
 */
class SolutionCategoryManager implements SolutionCategoryManagerInterface {

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
   * @var string
   */
  protected $currentLanguageCode;

  /**
   * Constructor.
   */
  public function __construct(QueryFactory $entity_query, EntityTypeManager $entity_type_manager, RouteMatchInterface $route_match, LanguageManagerInterface $languageManager) {
    $this->entityQuery = $entity_query;
    $this->entityTypeManager = $entity_type_manager;
    $this->routeMatch = $route_match;
    $this->currentLanguageCode = $languageManager->getCurrentLanguage()->getId();
  }

  /**
   * {@inheritdoc}
   */
  public function getCurrentTermId() {
    $category_id = 0;

    // Use the category term ID from the the current page.
    $node = $this->routeMatch->getParameter('node');
    if ($node && $node->hasField($this::TERM_SOLUTION_CATEGORY_FIELD)) {
      $category_id = $node->get($this::TERM_SOLUTION_CATEGORY_FIELD)->target_id;
    }

    return $category_id;
  }

  /**
   * {@inheritdoc}
   */
  public function getCurrentTerm() {
    $entity = NULL;

    $category_id = $this->getCurrentTermId();
    if ($category_id) {
      /** @var \Drupal\taxonomy\Entity\Term $entity */
      $entity = $this->entityTypeManager
        ->getStorage('taxonomy_term')
        ->load($category_id);
      if ($entity->hasTranslation($this->currentLanguageCode)) {
        $entity = $entity->getTranslation($this->currentLanguageCode);
      }
    }

    return $entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getByIndustry($industry_id) {
    $categories = array();
    $industry_ids = is_array($industry_id) ? $industry_id : [$industry_id];
    /** @var \Drupal\domain\Entity\Domain $domain */

    // Get category nodes that match current country and industry.
    $category_ids = $this->entityQuery->get('node')
      ->condition('type', $this::SOLUTION_CATEGORY_CONTENT_TYPE)
      ->condition('status', 1)
      ->condition($this::TERM_INDUSTRY_FIELD, $industry_ids, 'IN')
      ->addTag('node_access')
      ->sort('br_menu_link.entity.weight', 'ASC')
      ->sort('br_menu_link.entity.title', 'ASC')
      ->execute();

    if ($category_ids) {
      $categories = $this->entityTypeManager->getStorage('node')->loadMultiple($category_ids);
    }
    foreach ($categories as $key => $category) {
      if ($category->hasTranslation($this->currentLanguageCode)) {
        $categories[$key] = $category->getTranslation($this->currentLanguageCode);
      }
    }

    return $categories;
  }

}
