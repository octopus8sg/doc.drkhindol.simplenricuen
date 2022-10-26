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
    $tocheck = FALSE;
    if (U::checkHasNRICUENProfile($contribution_page_id)) {
        $tocheck = TRUE;
    }
    if (U::checkHasNRICProfile($contribution_page_id)) {
        $tocheck = TRUE;
    }
    if (U::checkHasUENProfile($contribution_page_id)) {
        $tocheck = TRUE;
    }
    if (!$tocheck) {
        return;
    }
    $element_name = 'external_identifier';
    U::removeRules($form, $element_name);
    $element_name = 'onbehalf_external_identifier';
    U::removeRules($form, $element_name);
    CRM_Core_Region::instance('contribution-main-not-you-block')->add(
        ['template' => 'CRM/Simplenricuen/Form/NRICUEN.tpl', 'weight' => +11]);
    CRM_Core_Resources::singleton()->addVars(
        'SimpleNRICUEN', [
        'testUEN' => U::checkHasUENProfile($contribution_page_id),
        'testNRIC' => U::checkHasNRICProfile($contribution_page_id)
    ]);

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
    if ($objectName !== 'Profile' && $objectName !== 'Individual' && $objectName !== 'Organization') {
        return;
    }
//    U::writeLog($params, 'params before');
    if ($objectName === 'Profile') {
        set_nricuen_profile($params);
        return;
    }

    if ($objectName === 'Individual' || $objectName === 'Organization') {
        set_nricuen_contact($params);
    }

    ///((S|T)([\d]{2})([A-Z]{2})([\d]{4})([A-Z])|(\d{9})([A-Z]))/g
}

/**
 * @param $params
 */
function set_nricuen_contact(&$params): void
{
//    U::writeLog('nricuen_individual', 'nricuen_individual start');

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
    replace_blank_first_last_name($params, $external_identifier);
    $external_identifier = $params['onbehalf_external_identifier'];
    replace_blank_first_last_name($params, $external_identifier);
    if (!array_key_exists("uen", $params)) {
        return;
    }
    set_nricuen_organization($params, $external_identifier);
    return;
}

/**
 * @param $params
 * @param $external_identifier
 */
function replace_blank_first_last_name(&$params, $external_identifier): void
{
    $profield_name = 'first_name';
    fill_profile_field($params, $profield_name, $external_identifier);
    $profield_name = 'last_name';
    fill_profile_field($params, $profield_name, $external_identifier);
}

/**
 * @param $params
 * @param $external_identifier
 */
function set_nricuen_organization(&$params, $external_identifier): void
{
    $pattern = "/^((S|T)([\d]{2})([A-Z]{2})([\d]{4})([A-Z])|(\d{9})([A-Z]))$/i";
    $preg_match = preg_match($pattern, $external_identifier); // Outputs 1
//        U::writeLog($preg_match, 'preg_match');
    if ($preg_match < 1) {
        return;
    }
    $profield_name = 'organization_name';
    fill_profile_field($params, $profield_name, $external_identifier);
    $params['contact_type'] = 'Organization';
//    U::writeLog($params, 'params after');
//    U::writeLog('nricuen_individual', 'nricuen_individual end');

    return;
}

/**
 * @param $params
 * @param string $profield_name
 * @param $needed_value
 * @return mixed
 */
function fill_profile_field(&$params, string $profield_name, $needed_value)
{
    $profile_field = CRM_Utils_Array::value($profield_name, $params);
    if (!$profile_field) {
        $params[$profield_name] = $needed_value;
    }
}

/**
 * @param $params
 */
function set_nricuen_profile(&$params): void
{
//    U::writeLog('nricuen_profile', 'nricuen_profile start');
    $contribution_page_id = get_contribution_page_id_for_profile($params);
    $params['contribution_page_id'] = $contribution_page_id;
    $tocheck = FALSE;
    if (U::checkHasNRICUENProfile($contribution_page_id)) {
        $tocheck = TRUE;
    }
    if (U::checkHasNRICProfile($contribution_page_id)) {
        $tocheck = TRUE;
    }
    if (U::checkHasUENProfile($contribution_page_id)) {
        $tocheck = TRUE;
    }
    if (!$tocheck) {
        return;
    }
    if (!array_key_exists("external_identifier", $params)) {
        return;
    }
    $external_identifier = $params['external_identifier'];
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
    if (U::checkHasUENProfile($contribution_page_id)) {
        $params['uen'] = true;
    }
    $external_identifier = $params['onbehalf_external_identifier'];
    if ($external_identifier) {
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
        if (U::checkHasUENProfile($contribution_page_id)) {
            $params['uen'] = true;
        }
    }
//    U::writeLog('nricuen_profile', 'nricuen_profile end');


}

/**
 * @param $params
 * @return int
 */
function get_contribution_page_id_for_profile($params): int
{
    U::writeLog($params, 'get_contribution_page_id_for_profile');
    $entryURLquery = [];
    $components = parse_url($params['entryURL']);
    parse_str(html_entity_decode($components['query']), $entryURLquery);
    $contribution_page_id = intval($entryURLquery['id']);
    return $contribution_page_id;
}
