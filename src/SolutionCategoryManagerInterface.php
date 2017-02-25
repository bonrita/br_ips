<?php

namespace Drupal\br_ips;

/**
 * Solution Category Manager interface.
 *
 * @package Drupal\br_solution
 */
interface SolutionCategoryManagerInterface {

  /**
   * The vocabulary that holds the Br Solution Category.
   */
  const SOLUTION_CATEGORY_VOCABULARY = 'solution_category';

  /**
   * The content type that holds the Br Solution Category.
   */
  // @todo Replace this by content type defined in BrGlobal.
  const SOLUTION_CATEGORY_CONTENT_TYPE = 'category';

  /**
   * The name of the field that holds the Solution Category taxonomy term.
   */
  const TERM_SOLUTION_CATEGORY_FIELD = 'field__term_category';

  /**
   * The name of the field that holds the Solution Category taxonomy term.
   */
  const TERM_INDUSTRY_FIELD = 'field__term_industry';

  /**
   * Gets the Term ID of the current category.
   *
   * @return integer
   */
  public function getCurrentTermId();

  /**
   * Gets the entity of the current category.
   *
   * @return \Drupal\taxonomy\Entity\Term|NULL
   */
  public function getCurrentTerm();

  /**
   * Gets the categories that are related to an industry.
   *
   * @param integer|integer[] $industry_id
   *   Industry taxonomy term ID(s).
   *
   * @return \Drupal\node\Entity\Node[]|NULL
   *   Array of Category entities.
   */
  public function getByIndustry($industry_id);

}
