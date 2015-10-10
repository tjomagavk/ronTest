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
        $output = 'Ценитель РОН';
    } else if ($status == 'Copper2') {
        $output = 'Знаток РОН';
    }
} else if (strstr($status, 'Silver')) {
    if ($status == 'Silver1') {
        $output = 'Мастер РОН';
    } else if ($status == 'Silver2') {
        $output = 'Эксперт РОН';
    }
} else if (strstr($status, 'Platinum')) {
    if ($status == 'Platinum1') {
        $output = 'Магистр Клуба';
    } else if ($status == 'Platinum2') {
        $output = 'Брендамбассадор';
    }
}
return $output;