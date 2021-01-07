<?php


{
    session_start();
    $code = $_POST['code'];
    if (strtolower($_SESSION['auth']) == $code) {
        exit(json_encode(array('code' => 1)));
    } else {
        exit(json_encode(array('code' => 0)));
    }

    unset($_SESSION['auth']);
}