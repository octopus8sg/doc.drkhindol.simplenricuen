<?php

require_once 'simplenricuen.civix.php';

// phpcs:disable
use CRM_Simplenricuen_ExtensionUtil as E;
use CRM_Simplenricuen_Utils as U;

// phpcs:enable

/**
 * Implements hook_civicrm_config().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_config/
 */
function simplenricuen_civicrm_config(&$config)
{
    _simplenricuen_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_install
 */
function simplenricuen_civicrm_install()
{
    _simplenricuen_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_postInstall
 */
function simplenricuen_civicrm_postInstall()
{
    _simplenricuen_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_uninstall
 */
function simplenricuen_civicrm_uninstall()
{
    _simplenricuen_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_enable
 */
function simplenricuen_civicrm_enable()
{
    _simplenricuen_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_disable
 */
function simplenricuen_civicrm_disable()
{
    _simplenricuen_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_upgrade
 */
function simplenricuen_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL)
{
    return _simplenricuen_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_entityTypes().
 *
 * Declare entity types provided by this module.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_entityTypes
 */
function simplenricuen_civicrm_entityTypes(&$entityTypes)
{
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
function simplenricuen_civicrm_navigationMenu(&$menu)
{
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

function simplenricuen_civicrm_buildForm($formName, &$form)
{
//    U::writeLog($formName, 'form name');
//    U::writeLog((Array)$form, 'form form');
    if ($formName !== 'CRM_Contribute_Form_Contribution_Main') {
        return;
    }
    $form_array = (Array)$form;
    $contribution_page_id = CRM_Utils_Array::value('_id', $form_array);
    $is_needed_profile = U::checkHasNRICUENProfile($contribution_page_id);
    if ($is_needed_profile) {
        $element_name = 'external_identifier';
        CRM_Simplenricuen_Utils::removeRules($form, $element_name);
        CRM_Core_Region::instance('contribution-main-not-you-block')->add(
            ['template' => 'CRM/Simplenricuen/Form/NRICUEN.tpl', 'weight' => +11]);
        CRM_Core_Resources::singleton()->addVars(
            'SimpleNRICUEN', [
            'testUEN' => U::getValidateUEN(),
            'testNRIC' => U::getValidateNRIC()
        ]);
    }

}

/**
 * @param $op
 * @param $objectName
 * @param $objectId
 * @param $params
 */
function simplenricuen_civicrm_pre($op, $objectName, $objectId, &$params)
{
//    U::writeLog($op, '$objectName');
//    U::writeLog($objectName, 'objectName');
//    U::writeLog($objectId, 'objectId');

    if ($op !== 'create') {
        return;
    }
    if ($objectName !== 'Profile' && $objectName !== 'Individual') {
        return;
    }
//    U::writeLog($params, 'params before');
    if ($objectName === 'Profile') {
        nricuen_profile($params);
        return;
    }
    if (!U::getValidateUEN()) {
        return;
    }
    if ($objectName === 'Individual') {
        nricuen_individual($params);
    }

    ///((S|T)([\d]{2})([A-Z]{2})([\d]{4})([A-Z])|(\d{9})([A-Z]))/g
}

/**
 * @param $params
 */
function nricuen_individual(&$params): void
{
    U::writeLog('nricuen_individual', 'nricuen_individual start');

    if (!array_key_exists("external_identifier", $params)) {
        return;
    }
    if (!array_key_exists("nric", $params)) {
        return;
    }
    if (!boolval($params['nric'])) {
        return;
    }
    $external_identifier = $params['external_identifier'];
    $pattern = "/^((S|T)([\d]{2})([A-Z]{2})([\d]{4})([A-Z])|(\d{9})([A-Z]))$/i";
    $preg_match = preg_match($pattern, $external_identifier); // Outputs 1
        U::writeLog($preg_match, 'preg_match');
    $first_name = CRM_Utils_Array::value('first_name', $params);
    if (!$first_name) {
        $params['first_name'] = $external_identifier;
    }
    $last_name = CRM_Utils_Array::value('last_name', $params);
    if (!$last_name) {
        $params['last_name'] = $external_identifier;
    }
    if ($preg_match < 1) {
        return;
    }
    $organization_name = CRM_Utils_Array::value('organization_name', $params);
    if (!$organization_name) {
        $params['organization_name'] = $external_identifier;
    }
    $params['contact_type'] = 'Organization';
//    U::writeLog($params, 'params after');
    U::writeLog('nricuen_individual', 'nricuen_individual end');

    return;
}

/**
 * @param $params
 */
function nricuen_profile(&$params): void
{
    U::writeLog('nricuen_profile', 'nricuen_profile start');
    $entryURLquery = [];
    $components = parse_url($params['entryURL']);
    parse_str(html_entity_decode($components['query']), $entryURLquery);
    $contribution_page_id = $entryURLquery['id'];
    $params['contribution_page_id'] = $contribution_page_id;
    $contribution_page_id = $params['contribution_page_id'];
    if (!U::checkHasNRICUENProfile($contribution_page_id)) {
        return;
    }
    if (!array_key_exists("external_identifier", $params)) {
        return;
    }
    $external_identifier = $params['external_identifier'];
    $params['external_identifier'] = $external_identifier;
    $contact = new CRM_Contact_DAO_Contact();
    $contact->external_identifier = $external_identifier;
    if ($contact->find(TRUE)) {
        $contact_id = $contact->id;
        $params['contactID'] = $contact_id;
        $params['contact_id'] = $contact_id;
        $primary_email = U::getPrimaryEmail($contact_id);
        $params['email-Primary-1'] = $primary_email;
        $params['email-Primary'] = $primary_email;
//        U::writeLog((array)$contact, 'contactNRIC');
    }
    $params['nric'] = true;
    U::writeLog('nricuen_profile', 'nricuen_profile end');


}
