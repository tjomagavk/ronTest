<?php
$output = true;

if (!empty($value)) {
    if (time() - $value < 18 * 31536000) {
        $errorMsg = '<span class="error">Возрастное ограничение: 18 лет</span>';
        $validator->addError($key, $errorMsg);

        // generate submission error
        $output = false;
    }
}
return $output;