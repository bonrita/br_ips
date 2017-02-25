<?php

namespace Drupal\br_ips;

use Drupal\br_menu\BrMenuLink;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\Entity\Query\QueryFactory;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\domain\DomainNegotiatorInterface;

/**
 * Platform Category manager service.
 *
 * @package Drupal\br_ips
 */
class PlatformCategoryManager implements PlatformCategoryManagerInterface {

  /**
   * @var \Drupal\Core\Entity\Query\QueryFactory
   */
  protected $entityQuery;

  /**
   * @var \Drupal\Core\Entity\EntityTypeManager
   */
  protected $entityTypeManager;

  /**
   * @var string
   */
  protected $currentLanguageCode;

  /**
   * Constructor.
   */
  public function __construct(QueryFactory $entity_query, EntityTypeManager $entity_type_manager, LanguageManagerInterface $languageManager) {
    $this->entityQuery = $entity_query;
    $this->entityTypeManager = $entity_type_manager;
    $this->currentLanguageCode = $languageManager->getCurrentLanguage()->getId();
  }

  /**
   * {@inheritdoc}
   *
   * @see
   */
  // @todo Add caching if this proves to be an expensive operation.
  public function menuTermOptionList() {
    $list = [];
    $tids = [];

    // Load all Platform Category terms (hierarchy of 2 levels deep).
    /** @var \Drupal\taxonomy\Entity\Term[] $terms */
    $terms = $this->entityTypeManager->getStorage('taxonomy_term')
      ->loadTree($this::PLATFORM_CATEGORY_VOCABULARY, 0, 2, TRUE);
    foreach ($terms as $term) {
      $tids[] = $term->id();
    }

    // Load the nodes that use these Platform Category terms (limited by
    // Platform content types and domain).
    $nids = $this->entityQuery->get('node')
      ->condition('type', [
        $this::PLATFORM_CATEGORY_CONTENT_TYPE,
        $this::PLATFORM_SPECIFICS_CONTENT_TYPE
      ], 'IN')
      ->condition('status', 1)
      ->condition($this::TERM_PLATFORM_CATEGORY_FIELD, $tids, 'IN')
      ->addTag('node_access')
      ->sort('br_menu_link.entity.weight', 'ASC')
      ->sort('br_menu_link.entity.title', 'ASC')
      ->execute();

    // Build a list terms keyed by tid and with the menu link title as value.
    /** @var \Drupal\node\Entity\Node[] $nodes */
    $nodes = $this->entityTypeManager->getStorage('node')->loadMultiple($nids);
    foreach ($nodes as $node) {
      $tid = $node->{$this::TERM_PLATFORM_CATEGORY_FIELD}->target_id;
      $list[$tid] = BrMenuLink::getTitle($node);
    }

    return $list;
  }

}
