<?php

namespace Drupal\br_ips;

/**
 * SolutionManager interface.
 *
 * @package Drupal\br_solution
 */
interface SolutionManagerInterface {

  /**
   * The vocabulary that holds the Br Solutions.
   */
  const SOLUTION_VOCABULARY = 'solution';

  /**
   * The content type that holds the Br Solution.
   */
  // @todo Replace this by content type defined in BrGlobal.
  const SOLUTION_CONTENT_TYPE = 'solution_specific';

  /**
   * The name of the field that holds the Solution taxonomy term.
   */
  const TERM_SOLUTION_FIELD = 'field__term_solution';

  /**
   * The name of the field that holds the Br Solution taxonomy term.
   */
  const TERM_SOLUTION_CATEGORY_FIELD = 'field__term_category';

  /**
   * Gets the Term ID of the current service.
   *
   * @return integer
   */
  public function getCurrentTermId();

  /**
   * Returns the Service taxonomy term that is valid for the current page.
   * @return \Drupal\taxonomy\Entity\Term|NULL
   */
  public function getCurrentTerm();

  /**
   * Gets the solutions that are related to a platform category.
   *
   * @param integer|integer[] $category_id
   *   Category term ID(s).
   *
   * @param integer|integer[] $industry_id
   *   Industry term ID(s).
   *
   * @return array
   *   Array of solution IDs.
   */
  public function getByCategory($category_id, $industry_id);

}
