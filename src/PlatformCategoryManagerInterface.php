<?php

namespace Drupal\br_ips;

/**
 * IndustryManager interface.
 *
 * @package Drupal\br_solution
 */
interface PlatformCategoryManagerInterface {

  /**
   * The vocabulary that holds the Platform Categories.
   */
  const PLATFORM_CATEGORY_VOCABULARY = 'platform';

  /**
   * The content type that holds the Platform Category.
   */
  const PLATFORM_CATEGORY_CONTENT_TYPE = 'platform';

  /**
   * The content type that holds the Platform Specifics.
   */
  const PLATFORM_SPECIFICS_CONTENT_TYPE = 'platform_specifics';

  /**
   * The name of the field that holds the Platform Category taxonomy term.
   */
  const TERM_PLATFORM_CATEGORY_FIELD = 'field__term_platform_category';

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
