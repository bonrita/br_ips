<?php

namespace Drupal\br_ips;

/**
 * Provides helpers to centrally manage groups of content types.
 *
 * @package Drupal\br_ips
 */
final class BrGlobal {

  const CT01_INDUSTRY = 'industry';
  const CT02_CATEGORY_PLATFORMS_OVERVIEW = 'platforms_overview';
  const CT03_CATEGORY_SOLUTIONS_OVERVIEW = 'solution';
  const CT04_CATEGORY_PLATFORM_GENERIC = 'platform';
  const CT05_CATEGORY_SOLUTIONS_GENERIC = 'category';
  const CT06_DETAIL_PLATFORM_SPECIFIC = 'platform_specifics';
  const CT07_DETAIL_SOLUTIONS_SPECIFIC = 'solution_specific';
  const PRESS_RELEASE_OVERVIEW = 'press_release_overview';
  const ARTICLE_STANDARD = 'article_standard';

  /**
   * The content types that have an icon in A01 Image Headline.
   *
   * @return array
   *   Array of content type machine names.
   */
  static public function contentTypesWithHeaderIcon() {
    return [
      self::CT02_CATEGORY_PLATFORMS_OVERVIEW,
      self::CT03_CATEGORY_SOLUTIONS_OVERVIEW,
      self::CT05_CATEGORY_SOLUTIONS_GENERIC,
      self::CT06_DETAIL_PLATFORM_SPECIFIC,
      self::CT07_DETAIL_SOLUTIONS_SPECIFIC,
    ];
  }

  /**
   * The content types that have a Solution Category icon.
   *
   * @return array
   *   Array of content type machine names.
   */
  static public function contentTypesWithSolutionCategoryIcon() {

    return [
      self::CT02_CATEGORY_PLATFORMS_OVERVIEW,
      self::CT03_CATEGORY_SOLUTIONS_OVERVIEW,
      self::CT05_CATEGORY_SOLUTIONS_GENERIC,
    ];
  }

  /**
   * The content types that have a Solution icon.
   *
   * @return array
   *   Array of content type machine names.
   */
  static public function contentTypesWithSolutionIcon() {

    return [
      self::CT06_DETAIL_PLATFORM_SPECIFIC,
      self::CT07_DETAIL_SOLUTIONS_SPECIFIC,
    ];
  }

  /**
   * The content types that do have a image.
   *
   * @return array
   *   Array of content type machine names.
   */
  static public function contentTypesWithHeaderImage() {
    return [
      self::CT01_INDUSTRY,
      self::CT04_CATEGORY_PLATFORM_GENERIC,
      self::PRESS_RELEASE_OVERVIEW,
      self::ARTICLE_STANDARD,
    ];
  }

}
