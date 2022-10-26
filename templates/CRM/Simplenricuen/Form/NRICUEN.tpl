{*
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC. All rights reserved.                        |
 |                                                                    |
 | This work is published under the GNU AGPLv3 license with some      |
 | permitted exceptions and without any warranty. For full license    |
 | and copyright information, see https://civicrm.org/licensing       |
 +--------------------------------------------------------------------+
*}


{*    {debug}*}


{* JS HERE*}
<script type="text/javascript">
    {literal}
    CRM.$(function ($) {
        // 'use strict';
        function uenValidator(uenInput) {
            const uen = uenInput.toUpperCase();
            const regexp = "((S|T)([\\d]{2})([A-Z]{2})([\\d]{4})([A-Z])|(\\d{9})([A-Z]))";
            const re = new RegExp(regexp);
            const uen_result = re.test(uenInput);
            return uen_result;
        }

        function nricValidator(nricInput) {
            // validation rules
            const nricRegex = /(\D)(\d{7})(\D)/;
            const nricTypeRegex = /S|T|F|G/;
            const weightArr = [2, 7, 6, 5, 4, 3, 2];
            const nricLetterST = ['J', 'Z', 'I', 'H', 'G', 'F', 'E', 'D', 'C', 'B', 'A'];
            const nricLetterFG = ['X', 'W', 'U', 'T', 'R', 'Q', 'P', 'N', 'M', 'L', 'K'];

            // set nric to all uppercase to remove case sensitivity
            const nric = nricInput.toUpperCase();

            // returns false if it false basic validation
            if (!nricRegex.exec(nric)) {
                return false;
            }

            // get nric type
            const nricArr = nric.match(nricRegex);
            const nricType = nricArr[1];

            // returns false if it false basic validation
            if (!nricTypeRegex.exec(nricType)) {
                return false;
            }

            // multiply each digit in the nric number by it's weight in order
            const nricDigitsArr = nricArr[2].split('');
            let total = 0;
            for (let i = 0; i < nricDigitsArr.length; i++) {
                total += parseInt(nricDigitsArr[i]) * weightArr[i];
            }

            // if the nric type is T or G, add 4 to the total
            if (['T', 'G'].indexOf(nricType) >= 0) {
                total += 4;
            }

            // check last letter of nric for local
            const letterIndex = total % 11;
            const nricLetter = nricArr[3];
            if (['S', 'T'].indexOf(nricType) >= 0) {
                return nricLetterST[letterIndex] === nricLetter;
            }

            // check last letter of nric for foreigners
            return nricLetterFG[letterIndex] === nricLetter;
        }

        $('#external_identifier').on('change', function (event, data) {

            const external_identifier = $(this).val();
            if (!external_identifier) {
                return;
            }
            if (0 === external_identifier.length) {
                return;
            }
            if (!this.value) {
                return;
            }

            const is_valid_nric = nricValidator(external_identifier);
            const is_valid_uen = uenValidator(external_identifier);
            // console.log(external_identifier);
            const testUEN = CRM.vars.SimpleNRICUEN.testUEN;
            const testNRIC = CRM.vars.SimpleNRICUEN.testNRIC;
            if (testUEN) {
                alert(is_valid_uen ? "Valid UEN" : "Not Valid UEN");
            }
            if (testNRIC) {
                alert(is_valid_nric ? "Valid NRIC" : "Not Valid NRIC");
            }
        });
        $('#onbehalf_external_identifier').on('change', function (event, data) {

            const external_identifier = $(this).val();
            if (!external_identifier) {
                return;
            }
            if (0 === external_identifier.length) {
                return;
            }
            if (!this.value) {
                return;
            }

            const is_valid_nric = nricValidator(external_identifier);
            const is_valid_uen = uenValidator(external_identifier);
            // console.log(external_identifier);
            const testUEN = CRM.vars.SimpleNRICUEN.testUEN;
            const testNRIC = CRM.vars.SimpleNRICUEN.testNRIC;
            if (testUEN) {
                alert(is_valid_uen ? "Valid UEN" : "Not Valid UEN");
            }
            if (testNRIC) {
                alert(is_valid_nric ? "Valid NRIC" : "Not Valid NRIC");
            }
        });
        $('#onbehalf[external_identifier]').on('change', function (event, data) {

            const external_identifier = $(this).val();
            if (!external_identifier) {
                return;
            }
            if (0 === external_identifier.length) {
                return;
            }
            if (!this.value) {
                return;
            }

            const is_valid_nric = nricValidator(external_identifier);
            const is_valid_uen = uenValidator(external_identifier);
            // console.log(external_identifier);
            const testUEN = CRM.vars.SimpleNRICUEN.testUEN;
            const testNRIC = CRM.vars.SimpleNRICUEN.testNRIC;
            if (testUEN) {
                alert(is_valid_uen ? "Valid UEN" : "Not Valid UEN");
            }
            if (testNRIC) {
                alert(is_valid_nric ? "Valid NRIC" : "Not Valid NRIC");
            }
        });

    });


    {/literal}
</script>
