<?php

use CRM_Simpleanonymous_ExtensionUtil as E;

use \Firebase\JWT\JWT;

class CRM_Simpleanonymous_Utils
{


    /**
     * @param $input
     * @param $preffix_log
     */
    public static function writeLog($input, $preffix_log = "Simple Anonymous Log")
    {
        try {
            $simpleanonymous_settings = CRM_Core_BAO_Setting::getItem("Simple Anonymous Settings", 'simpleanonymous_settings');
            if ($simpleanonymous_settings['save_log'] == '1') {
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
            $error_title = 'Anonymous Configuration Required';
            CRM_Simpleanonymous_Utils::showErrorMessage($error_message, $error_title);
        }
    }

    /**
     * @param $contribution_page_id
     * @return bool
     */
    public static function checkHasAnonymousProfile($contribution_page_id)
    {
        try {
            $result = FALSE;
            $profile = self::getAnonymousProfileID();
            //        CRM_Simpleanonymous_Utils::write_log($params, 'create params');
            $ufJoinParams = [
                'entity_table' => 'civicrm_contribution_page',
                'uf_group_id' => $profile,
                'entity_id' => $contribution_page_id,
            ];

            $ufJoinParams['module'] = 'CiviContribute';
            $ufJoin = new CRM_Core_DAO_UFJoin();
            $ufJoin->copyValues($ufJoinParams);
            $ufJoin->find(TRUE);
            if ($ufJoin->is_active) {
                $result = TRUE;
            }
            CRM_Simpleanonymous_Utils::writeLog(strval($result), 'check_if_has_anon_profile');
            return $result;
        } catch (\Exception $exception) {
            $error_message = $exception->getMessage();
            $error_title = 'Anonymous Profile Required';
            CRM_Simpleanonymous_Utils::showErrorMessage($error_message, $error_title);
        }
    }

    /**
     * @return mixed
     */

    public static function getAnonymousUserID()
    {
//        $result = FALSE;
        try {
            $simpleanonymous_settings = CRM_Core_BAO_Setting::getItem("Simple Anonymous Settings", 'simpleanonymous_settings');
            $result = $simpleanonymous_settings['anonynomous_id'];
            return $result;
        } catch (\Exception $exception) {
            $error_message = $exception->getMessage();
            $error_title = 'Anonymous User Required';
            CRM_Simpleanonymous_Utils::showErrorMessage($error_message, $error_title);
        }
    }

    /**
     * @return mixed
     */
    public static function getAnonymousProfileID()
    {
//        $result = FALSE;
        try {
            $simpleanonymous_settings = CRM_Core_BAO_Setting::getItem("Simple Anonymous Settings", 'simpleanonymous_settings');
            $result = $simpleanonymous_settings['profile'];
            return $result;
        } catch (\Exception $exception) {
            $error_message = $exception->getMessage();
            $error_title = 'Anonymous Profile Required';
            CRM_Simpleanonymous_Utils::showErrorMessage($error_message, $error_title);
        }
    }

    /**
     * @return bool
     */
    public static function getHideEmail()
    {
        $result = FALSE;
        try {
            $simpleanonymous_settings = CRM_Core_BAO_Setting::getItem("Simple Anonymous Settings", 'simpleanonymous_settings');
            $result_ = $simpleanonymous_settings['hide_email'];
            if ($result_ == 1) {
                $result = TRUE;
            }
            return $result;
        } catch (\Exception $exception) {
            $error_message = $exception->getMessage();
            $error_title = 'Anonymous Profile Required';
            CRM_Simpleanonymous_Utils::showErrorMessage($error_message, $error_title);
        }
    }

    /**
     * @return bool
     */
    public static function getHideProfile()
    {
        $result = FALSE;
        try {
            $simpleanonymous_settings = CRM_Core_BAO_Setting::getItem("Simple Anonymous Settings", 'simpleanonymous_settings');
            $result_ = $simpleanonymous_settings['hide_profile'];
            if ($result_ == 1) {
                $result = TRUE;
            }
            return $result;
        } catch (\Exception $exception) {
            $error_message = $exception->getMessage();
            $error_title = 'Anonymous Profile Required';
            CRM_Simpleanonymous_Utils::showErrorMessage($error_message, $error_title);
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

}