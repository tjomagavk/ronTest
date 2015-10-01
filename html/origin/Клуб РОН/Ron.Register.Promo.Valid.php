<?php
$output = true;
if (!empty($value)) {

    if ($value == 'silver') {
        $fields['statusMember'] = 'Silver1';
    } else {
        $errorMsg = '<span class="error">¬веденный премиум-код неверен</span>';
        $validator->addError($key, $errorMsg);

        // generate submission error
        $output = false;
    }
}
return $output;