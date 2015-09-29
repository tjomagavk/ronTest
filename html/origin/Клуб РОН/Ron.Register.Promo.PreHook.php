<?php
$fields = $hook->getValues();
if (!empty($fields['promo'])) {

    if ($fields['promo'] == 'silver') {
        $hook->setValue('statusMember', 'Silver1');
    }
}else {
    $hook->setValue('statusMember', 'Copper1');
}
return true;