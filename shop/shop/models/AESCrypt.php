<?php
/**
 * Created by PhpStorm.
 * Author: Michael Ma
 * Date: 2018年08月10日
 * Time: 16:36:12
 */
/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
/* AES implementation in PHP (c) Chris Veness 2005-2011. Right of free use is granted for all  */
/*  commercial or non-commercial use under CC-BY licence. No warranty of any form is offered.  */
/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
/* aes 256 encrypt
* @param String $ostr
* @param String $securekey
* @param String $type encrypt, decrypt
*/
function aes($ostr, $securekey, $type = 'encrypt')
{
    if ($ostr == '') {
        return '';
    }
    $key = $securekey;
    $iv = strrev($securekey);
    $td = mcrypt_module_open('rijndael-256', '', 'ofb', '');
    mcrypt_generic_init($td, $key, $iv);
    $str = '';
    switch ($type) {
        case 'encrypt':
            $str = base64_encode(mcrypt_generic($td, $ostr));
            break;
        case 'decrypt':
            $str = mdecrypt_generic($td, base64_decode($ostr));
            break;
    }
    mcrypt_generic_deinit($td);
    
    return $str;
}

// Demo
$key = "fdipzone201314showmethemoney!@#$";
$str = "show me the money";
$ostr = aes($str, $key);
echo "String 1: $ostr<br />";
$dstr = aes($ostr, $key, 'decrypt');
echo "String 2: $dstr<br />";