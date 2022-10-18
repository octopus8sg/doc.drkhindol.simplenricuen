<?php

use CRM_Simpleanonymous_ExtensionUtil as E;

use \Firebase\JWT\JWT;

class CRM_Simplenricuen_Utils
{
    public const NRIC_PROFILES = 'nric_profiles';
    public const UEN_PROFILES = 'uen_profiles';
    public const PROFILES = 'profiles';
    public const SAVE_LOG = 'save_log';
    public const VALIDATE_UEN = 'validate_uen';
    public const VALIDATE_NRIC = 'validate_nric';

    /**
     * @param $input
     * @param $preffix_log
     */
    public static function writeLog($input, $preffix_log = "Simple NRICUEN Log")
    {
        try {
            if (self::getSaveLog()) {
                $masquerade_input = $input;
                if (is_array($masquerade_input)) {
                    $fields_to_hide = ['Signature'];
                    foreach ($fields_to_hide as $field_to_hide) {
                        unset($masquerade_input[$field_to_hide]);
                    }
                    Civi::log()->debug($preffix_log . "\n" . print_r($masquerade_input, TRUE));
                    return;
                }
                Civi::log()->debug($preffix_log . "\n" . $masquerade_input);
                return;
            }
        } catch (\Exception $exception) {
            $error_message = $exception->getMessage();
            $error_title = 'Simple NRICUEN Configuration Required';
            self::showErrorMessage($error_message, $error_title);
        }
    }

    /**
     * @param $contribution_page_id
     * @return bool
     */
    public static function checkHasNRICUENProfile($contribution_page_id)
    {
        if (!is_numeric($contribution_page_id)) {
            return FALSE;
        }
        try {
            $result = FALSE;
            $profiles = self::getNRICUENProfileIDS();
//            self::writeLog($profiles, 'profiles');
            $ufJoinParams = [
                'entity_table' => 'civicrm_contribution_page',
//                'uf_group_id' => 'IN [1,16]',
                'entity_id' => $contribution_page_id,
            ];

            $ufJoinParams['module'] = 'CiviContribute';
            $ufJoin = new CRM_Core_DAO_UFJoin();
            $ufJoin->copyValues($ufJoinParams);
            $ufJoin->whereAddIn('uf_group_id', $profiles, 'int');
//            self::writeLog((array)$ufJoin, 'query');
            $ufJoin->find(TRUE);
            if ($ufJoin->is_active) {
                $result = TRUE;
            }
//            self::writeLog(strval($result), 'checkHasNRICUENProfile');
            return $result;
        } catch (\Exception $exception) {
            $error_message = $exception->getMessage();
            $error_title = 'NRICUEN Profile Required';
            self::showErrorMessage($error_message, $error_title);
        }
    }

    /**
     * @param $contribution_page_id
     * @return bool
     */
    public static function checkHasUENProfile($contribution_page_id)
    {
        if (!is_numeric($contribution_page_id)) {
            return FALSE;
        }
        try {
            $result = FALSE;
            $profiles = self::getUENProfileIDS();
//            self::writeLog($profiles, 'profiles');
            $ufJoinParams = [
                'entity_table' => 'civicrm_contribution_page',
//                'uf_group_id' => 'IN [1,16]',
                'entity_id' => $contribution_page_id,
            ];

            $ufJoinParams['module'] = 'CiviContribute';
            $ufJoin = new CRM_Core_DAO_UFJoin();
            $ufJoin->copyValues($ufJoinParams);
            $ufJoin->whereAddIn('uf_group_id', $profiles, 'int');
//            self::writeLog((array)$ufJoin, 'query');
            $ufJoin->find(TRUE);
            if ($ufJoin->is_active) {
                $result = TRUE;
            }
//            self::writeLog(strval($result), 'checkHasNRICUENProfile');
            return $result;
        } catch (\Exception $exception) {
            $error_message = $exception->getMessage();
            $error_title = 'NRICUEN Profile Required';
            self::showErrorMessage($error_message, $error_title);
        }
    }

    /**
     * @param $contribution_page_id
     * @return bool
     */
    public static function checkHasNRICProfile($contribution_page_id)
    {
        if (!is_numeric($contribution_page_id)) {
            return FALSE;
        }
        try {
            $result = FALSE;
            $profiles = self::getNRICProfileIDS();
//            self::writeLog($profiles, 'profiles');
            $ufJoinParams = [
                'entity_table' => 'civicrm_contribution_page',
//                'uf_group_id' => 'IN [1,16]',
                'entity_id' => $contribution_page_id,
            ];

            $ufJoinParams['module'] = 'CiviContribute';
            $ufJoin = new CRM_Core_DAO_UFJoin();
            $ufJoin->copyValues($ufJoinParams);
            $ufJoin->whereAddIn('uf_group_id', $profiles, 'int');
//            self::writeLog((array)$ufJoin, 'query');
            $ufJoin->find(TRUE);
            if ($ufJoin->is_active) {
                $result = TRUE;
            }
//            self::writeLog(strval($result), 'checkHasNRICUENProfile');
            return $result;
        } catch (\Exception $exception) {
            $error_message = $exception->getMessage();
            $error_title = 'NRICUEN Profile Required';
            self::showErrorMessage($error_message, $error_title);
        }
    }


    /**
     * @return mixed
     */
    public static function getNRICUENProfileIDS()
    {
//        $result = FALSE;
        try {
            $simple_settings = CRM_Core_BAO_Setting::getItem("Simple NRICUEN Settings", 'simplenricuen_settings');
            $aresult = $simple_settings[self::PROFILES];
            $result = CRM_utils_array::explodePadded($simple_settings['profiles'], ',');
            return $result;
        } catch (\Exception $exception) {
            $error_message = $exception->getMessage();
            $error_title = 'Anonymous Profile Required';
            self::showErrorMessage($error_message, $error_title);

        }
    }

    /**
     * @return mixed
     */
    public static function getUENProfileIDS()
    {
//        $result = FALSE;
        try {
            $simple_settings = CRM_Core_BAO_Setting::getItem("Simple NRICUEN Settings", 'simplenricuen_settings');
            $aresult = $simple_settings[self::UEN_PROFILES];
            $result = CRM_utils_array::explodePadded($simple_settings['profiles'], ',');
            return $result;
        } catch (\Exception $exception) {
            $error_message = $exception->getMessage();
            $error_title = 'Anonymous Profile Required';
            self::showErrorMessage($error_message, $error_title);

        }
    }

    /**
     * @return mixed
     */
    public static function getNRICProfileIDS()
    {
//        $result = FALSE;
        try {
            $simple_settings = CRM_Core_BAO_Setting::getItem("Simple NRICUEN Settings", 'simplenricuen_settings');
            $aresult = $simple_settings[self::NRIC_PROFILES];
            $result = CRM_utils_array::explodePadded($simple_settings['profiles'], ',');
            return $result;
        } catch (\Exception $exception) {
            $error_message = $exception->getMessage();
            $error_title = 'Anonymous Profile Required';
            self::showErrorMessage($error_message, $error_title);

        }
    }


    /**
     * @return bool
     */
    public static function getSaveLog(): bool
    {
        $result = false;
        try {
            $simple_settings = CRM_Core_BAO_Setting::getItem("Simple NRICUEN Settings", 'simplenricuen_settings');
            $result_ = $simple_settings[self::SAVE_LOG];
            if ($result_ == 1) {
                $result = true;
            }
            return $result;
        } catch (\Exception $exception) {
            $error_message = $exception->getMessage();
            $error_title = 'Write Log Config Required';
            self::showErrorMessage($error_message, $error_title);
        }
    }

    /**
     * @return bool
     */
    public static function getValidateUEN(): bool
    {
        $result = false;
        try {
            $simple_settings = CRM_Core_BAO_Setting::getItem("Simple NRICUEN Settings", 'simplenricuen_settings');
            $result_ = $simple_settings[self::VALIDATE_UEN];
//            self::writeLog($result, 'getValidateUEN');
            if ($result_ == 1) {
                $result = true;
            }
            return $result;
        } catch (\Exception $exception) {
            $error_message = $exception->getMessage();
            $error_title = 'Write Log Config Required';
            self::showErrorMessage($error_message, $error_title);
        }
    }

    /**
     * @return bool
     */
    public static function getValidateNRIC(): bool
    {
        $result = false;
        try {
            $simple_settings = CRM_Core_BAO_Setting::getItem("Simple NRICUEN Settings", 'simplenricuen_settings');
            $result_ = $simple_settings[self::VALIDATE_NRIC];
//            self::writeLog($result, 'getValidateNRIC');
            if ($result_ == 1) {
                $result = true;
            }
            return $result;
        } catch (\Exception $exception) {
            $error_message = $exception->getMessage();
            $error_title = 'Write Log Config Required';
            self::showErrorMessage($error_message, $error_title);
        }
    }

    /**
     * @param string $error_message
     * @param string $error_title
     */
    public static function showErrorMessage(string $error_message, string $error_title): void
    {
        $session = CRM_Core_Session::singleton();
        $userContext = $session->readUserContext();
        CRM_Core_Session::setStatus($error_message, $error_title, 'error');
        CRM_Utils_System::redirect($userContext);
    }

    public static function getPrimaryEmail($uid)
    {
        $primary = '';
        $emails = CRM_Core_BAO_Email::allEmails($uid);
        foreach ($emails as $eid => $e) {
            if ($e['is_primary']) {
                if ($e['email']) {
                    $primary = $e['email'];
                    break;
                }
            }

            if (count($emails) == 1) {
                $primary = $e['email'];
                break;
            }
        }
        return $primary;
    }

    /**
     * @param $form
     * @param string $element_name
     */
    public static function removeRules(&$form, string $element_name): void
    {
        $element = $form->getElement($element_name);
        $form->removeElement($element_name, true);
        $form->addElement($element);
    }

}