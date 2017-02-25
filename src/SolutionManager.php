<?php

namespace Drupal\br_ips;

use Drupal\Core\Entity\Query\QueryFactory;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\domain\DomainNegotiatorInterface;

/**
 * Class Solution.
 *
 * @package Drupal\br_ips
 */
class SolutionManager implements SolutionManagerInterface {

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
    $solution_id = 0;

    // Use the category term ID from the the current page.
    $node = $this->routeMatch->getParameter('node');
    if ($node && $node->hasField($this::TERM_SOLUTION_FIELD)) {
      $solution_id = $node->get($this::TERM_SOLUTION_FIELD)->target_id;
    }

    return $solution_id;
  }

  /**
   * {@inheritdoc}
   */
  public function getCurrentTerm() {
    $entity = NULL;

    $solution_id = $this->getCurrentTermId();
    if ($solution_id) {
      if ($solution_id) {
        /** @var \Drupal\taxonomy\Entity\Term $entity */
        $entity = $this->entityTypeManager
          ->getStorage('taxonomy_term')
          ->load($solution_id);
        if ($entity->hasTranslation($this->currentLanguageCode)) {
          $entity = $entity->getTranslation($this->currentLanguageCode);
        }
      }
    }

    return $entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getByCategory($category_id, $industry_id) {
    $nodes = [];
    $category_ids = is_array($category_id) ? $category_id : [$category_id];
    $industry_ids = is_array($industry_id) ? $industry_id : [$industry_id];

    // Get solution nodes that match current country and category.
    $solution_ids = $this->entityQuery->get('node')
      ->condition('type', $this::SOLUTION_CONTENT_TYPE)
      ->condition('status', 1)
      ->condition($this::TERM_SOLUTION_CATEGORY_FIELD, $category_ids, 'IN')
      ->condition("field__term_industry", $industry_ids, 'IN')
      ->addTag('node_access')
      ->sort('sticky', 'DESC')
      ->sort('br_menu_link.entity.weight', 'ASC')
      ->sort('br_menu_link.entity.title', 'ASC')
      ->execute();

    if ($solution_ids) {
      /** @var \Drupal\node\Entity\Node[] $nodes */
      $nodes = $this->entityTypeManager->getStorage('node')->loadMultiple($solution_ids);
      foreach ($nodes as $key => $node) {
        if ($node->hasTranslation($this->currentLanguageCode)) {
          $entities[$key] = $node->getTranslation($this->currentLanguageCode);
        }
      }
    }

    return $nodes;
  }

  public function getSolutionCount($category_id, $industry_id) {

    $category_ids = is_array($category_id) ? $category_id : [$category_id];
    $industry_ids = is_array($industry_id) ? $industry_id : [$industry_id];

    // Get solution nodes that match current country and category.
    $solution_count = $this->entityQuery->get('node')
      ->condition('type', $this::SOLUTION_CONTENT_TYPE)
      ->condition('status', 1)
      ->condition($this::TERM_SOLUTION_CATEGORY_FIELD, $category_ids, 'IN')
      ->condition("field__term_industry", $industry_ids, 'IN')
      ->addTag('node_access')
      ->count()
      ->execute();

    return $solution_count;
  }


}
