<?php

require_once 'simplenricuen.civix.php';
// phpcs:disable
use CRM_Simplenricuen_ExtensionUtil as E;
// phpcs:enable

/**
 * Implements hook_civicrm_config().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_config/
 */
function simplenricuen_civicrm_config(&$config) {
  _simplenricuen_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_install
 */
function simplenricuen_civicrm_install() {
  _simplenricuen_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_postInstall
 */
function simplenricuen_civicrm_postInstall() {
  _simplenricuen_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_uninstall
 */
function simplenricuen_civicrm_uninstall() {
  _simplenricuen_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_enable
 */
function simplenricuen_civicrm_enable() {
  _simplenricuen_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_disable
 */
function simplenricuen_civicrm_disable() {
  _simplenricuen_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_upgrade
 */
function simplenricuen_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _simplenricuen_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_entityTypes().
 *
 * Declare entity types provided by this module.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_entityTypes
 */
function simplenricuen_civicrm_entityTypes(&$entityTypes) {
  _simplenricuen_civix_civicrm_entityTypes($entityTypes);
}

// --- Functions below this ship commented out. Uncomment as required. ---

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_preProcess
 */
//function simplenricuen_civicrm_preProcess($formName, &$form) {
//
//}

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_navigationMenu
 */
function simplenricuen_civicrm_navigationMenu(&$menu) {
  _simplenricuen_civix_insert_navigation_menu($menu, 'Administer/Customize Data and Screens', [
      'label' => E::ts('Configure Simple NRIC/UEN Field'),
      'name' => 'settings_simple_nricuen',
      'url' => 'civicrm/simplenricuen/settings',
      'permission' => 'adminster CiviCRM',
      'operator' => 'OR',
      'separator' => 1,
  ]);
  _simplenricuen_civix_navigationMenu($menu);
}
