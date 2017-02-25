<?php

namespace Drupal\br_ips;

/**
 * IndustryManager interface.
 *
 * @package Drupal\br_solution
 */
interface IndustryManagerInterface {


  /**
   * The vocabulary that holds the Br Industry.
   */
  const INDUSTRY_VOCABULARY = 'industry';

  /**
   * The content type that holds the Br Industry.
   */
  const INDUSTRY_CONTENT_TYPE = 'industry';

  /**
   * The name of the field that holds the Br Industry taxonomy term.
   */
  const TERM_INDUSTRY_FIELD = 'field__term_industry';

  /**
   * Load the user preferred industry stored in a cookie.
   *
   * @return string
   *   Industry code or empty when not set.
   */
  public function getPreferredIndustry();

  /**
   * Stores the preferred industry in a cookie.
   *
   * @param string $industry
   *   Industry code.
   */
  public function setPreferredIndustry($industry);

  /**
   * Gets the Term ID of the current industry.
   *
   * @return integer
   */
  public function getCurrentTermId();

  /**
   * Returns the Industry taxonomy term that is valid for the current page.
   *
   * @return NULL|\Drupal\taxonomy\Entity\Term
   */
  public function getCurrentTerm();

  /**
   * Returns the node of the currently active industry.
   *
   * @return \Drupal\node\Entity\Node
   *   The Industry node.
   */
  public function getCurrentNode();

    /**
   * Gets the Industry nodes that matches the current country.
   *
   * @return array
   *   Array of industry nodes, order by country specific term weight.
   */
  public function getCountryIndustries();

  /**
   * Gets an option list of terms using menu item texts.
   *
   * @return array
   *   Associative array of Platform Category terms where the keys are term IDs
   *   and the values are menu item texts that correspond to the Platform
   *   Category or Platform Specifics nodes tagged with these terms.
   */
  public function menuTermOptionList();

}
