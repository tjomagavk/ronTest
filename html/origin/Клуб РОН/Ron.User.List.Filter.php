<?php
/**
 * Created by PhpStorm.
 * User: tjomagavk
 * Date: 09.10.2015
 * Time: 1:16
 */
$output = '';
if (!empty($_GET['statusMember'])) {
    $output .= '"modUserProfile.extended:LIKE":"%\"statusMember\":\"' . $_GET['statusMember'] . '\"%"';
}
if (!empty($_GET['fullname'])) {
    if (!empty($output)) {
        $output .= ',"AND:';
    } else {
        $output .= '"';
    }
    $output .= 'modUserProfile.fullname:LIKE":"%' . $_GET['fullname'] . '%"';
}
if (!empty($_GET['username'])) {
    if (!empty($output)) {
        $output .= ',"AND:';
    } else {
        $output .= '"';
    }
    $output .= 'modUserProfile.username:LIKE":"%' . $_GET['username'] . '%"';
}
if (!empty($_GET['email'])) {
    if (!empty($output)) {
        $output .= ',"AND:';
    } else {
        $output .= '"';
    }
    $output .= 'modUserProfile.email:LIKE":"%' . $_GET['email'] . '%"';
}
if (!empty($_GET['cardnumber'])) {
    if (!empty($output)) {
        $output .= ',"AND:';
    } else {
        $output .= '"';
    }
    $output .= 'modUserProfile.id:LIKE":"' . ($_GET['cardnumber'] - 53500) . '"';
}
$output = '{' . $output . '}';
return $output;