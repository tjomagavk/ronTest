<?php
/**
 * Created by PhpStorm.
 * User: tjomagavk
 * Date: 06.10.2015
 * Time: 21:57
 */
$output = '';
if (strstr($status, 'Copper')) {
    if ($status == 'Copper1') {
        $output = '�������� ���';
    } else if ($status == 'Copper2') {
        $output = '������ ���';
    }
} else if (strstr($status, 'Silver')) {
    if ($status == 'Silver1') {
        $output = '������ ���';
    } else if ($status == 'Silver2') {
        $output = '������� ���';
    }
} else if (strstr($status, 'Platinum')) {
    if ($status == 'Platinum1') {
        $output = '������� �����';
    } else if ($status == 'Platinum2') {
        $output = '���������������';
    }
}
return $output;