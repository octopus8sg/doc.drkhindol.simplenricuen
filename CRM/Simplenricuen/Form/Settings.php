<?php

use CRM_Simplenricuen_ExtensionUtil as E;
use CRM_Simplenricuen_Utils as U;

/**
 * Form controller class
 *
 * @see https://docs.civicrm.org/dev/en/latest/framework/quickform/
 */
class CRM_Simplenricuen_Form_Settings extends CRM_Core_Form
{
    protected $_userContext;

    public function buildQuickForm()
    {
        $save_log = $this->add('checkbox', U::SAVE_LOG, 'Save extension debug to log');
//        $validate_uen = $this->add('checkbox', 'validate_uen', 'Validate UEN (create Org Contact if input matches UEN)');
//        $validate_nric = $this->add('checkbox', 'validate_nric', 'Validate NRIC (create Ind Contact if input matches NRIC)');
//        $anonynomous_email = $this->add('email', 'anonynomous_email', 'Anonynomous Email', ['size' => 100]);

        $types = ['Contact'];
        $all_profiles = CRM_Core_BAO_UFGroup::getValidProfiles($types);
        if (empty($all_profiles)) {
            $error_message = "You will need to create a Profile for Simple NRIC/UEN Field. Navigate to Administer CiviCRM > Customize Data and Screens > CiviCRM Profile to configure a Profile. Consult the online Administrator documentation for more information.";
            $error_title = 'Profile Required';
            U::showErrorMessage($error_message, $error_title);
        }
//        U::writeLog($all_profiles, 'All Profiles', );
        $new_profiles = [];
        foreach ($all_profiles as $key => $profile) {
            $new_profiles[] = ['id' => $key, 'text' => $profile, 'description' => $profile];
        }
//        U::writeLog($new_profiles, 'New Profiles', );

        $profiles = $this->add('select2', U::PROFILES, ts('Profiles To Apply'), $new_profiles, FALSE,
            ['placeholder' => ts('Select Profiles'), 'class' => 'huge', 'multiple' => 'multiple']
        );
//        $types = ['Individual'];
//        $all_profiles = CRM_Core_BAO_UFGroup::getValidProfiles($types);
//        $new_profiles = [];
//        foreach ($all_profiles as $key => $profile) {
//            $new_profiles[] = ['id' => $key, 'text' => $profile, 'description' => $profile];
//        }

        $nric_profiles = $this->add('select2', U::NRIC_PROFILES, ts('Profiles To Validate NRIC'), $new_profiles, FALSE,
            ['placeholder' => ts('Select Profiles'), 'class' => 'huge', 'multiple' => 'multiple']
        );
//        $types = ['Organization'];
//        $all_profiles = CRM_Core_BAO_UFGroup::getValidProfiles($types);
//        $new_profiles = [];
//        foreach ($all_profiles as $key => $profile) {
//            $new_profiles[] = ['id' => $key, 'text' => $profile, 'description' => $profile];
//        }
        $uen_profiles = $this->add('select2', U::UEN_PROFILES, ts('Profiles To Validate UEN'), $new_profiles, FALSE,
            ['placeholder' => ts('Select Profiles'), 'class' => 'huge', 'multiple' => 'multiple']
        );

        $this->addButtons([
            [
                'type' => 'submit',
                'name' => E::ts('Submit'),
                'isDefault' => TRUE,
            ],
        ]);
        $this->assign('elementNames', $this->getRenderableElementNames());
        parent::buildQuickForm();
    }

    public function setDefaultValues()
    {
        $defaults = [];
        $simple_settings = CRM_Core_BAO_Setting::getItem("Simple NRICUEN Settings", 'simplenricuen_settings');
        if (!empty($simple_settings)) {
            $defaults = $simple_settings;
//            $defaults['profiles'] = "";
        }
        return $defaults;
    }

    public function postProcess()
    {

        $values = $this->exportValues();
        $simple_settings = [];
        $simple_settings[U::SAVE_LOG] = $values[U::SAVE_LOG];
//        $simple_settings['validate_uen'] = $values['validate_uen'];
//        $simple_settings['validate_nric'] = $values['validate_nric'];
        $simple_settings[U::PROFILES] = $values[U::PROFILES];
        $simple_settings[U::NRIC_PROFILES] = $values[U::NRIC_PROFILES];
        $simple_settings[U::UEN_PROFILES] = $values[U::UEN_PROFILES];


        CRM_Core_BAO_Setting::setItem($simple_settings, "Simple NRICUEN Settings", 'simplenricuen_settings');
        CRM_Core_Session::setStatus(E::ts('Simple NRIC/UEN Settings Saved', ['domain' => 'com.drkhindol.simplenricuen']), 'Configuration Updated', 'success');

        parent::postProcess();
    }

    /**
     * Get the fields/elements defined in this form.
     *
     * @return array (string)
     */
    public function getRenderableElementNames()
    {
        // The _elements list includes some items which should not be
        // auto-rendered in the loop -- such as "qfKey" and "buttons".  These
        // items don't have labels.  We'll identify renderable by filtering on
        // the 'label'.
        $elementNames = array();
        foreach ($this->_elements as $element) {
            /** @var HTML_QuickForm_Element $element */
            $label = $element->getLabel();
            if (!empty($label)) {
                $elementNames[] = $element->getName();
            }
        }
        return $elementNames;
    }


}
