<?php
/**
 * @author    Greg Arnold
 * @license   http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 * @copyright Grag Arnold <greg@arnoldassociates.com>
 */

function unix_crypt($password) {
  global $cfg;
  $chars = './0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
  $encrypt_method = is_array($cfg) && array_key_exists('encrypt_method', $cfg) ? $cfg['encrypt_method'] : "MD5";
  $salt = "";

  if ($cfg['read_login_defs']) {
    $login_defs = file_get_contents("/etc/login.defs");
    if ($login_defs !== -1) {
      if (preg_match('/[\r\n]+\s*ENCRYPT_METHOD\s+(.*?)[\r\n]/', $login_defs, $matches))
        $encrypt_method = $matches[1];

      /* XXX: Read number of rounds from login.defs */
    }
  }

  switch ($encrypt_method) {    
    case "MD5":
      $salt = '$1$';
      for ($i = 0; $i < 8; $i++)
        $salt .= $chars[mt_rand(0, strlen($chars) - 1)];
      $salt .= '$';
      break;
    case "SHA256":
      $salt = '$5$';
      $len = mt_rand(8, 16);
      for ($i = 0; $i < $len; $i++)
        $salt .= $chars[mt_rand(0, strlen($chars) - 1)];
      $salt .= '$';
      break;
    case "SHA512":
      $salt = '$6$';
      $len = mt_rand(8, 16);
      for ($i = 0; $i < $len; $i++)
        $salt .= $chars[mt_rand(0, strlen($chars) - 1)];
      $salt .= '$';
      break;
    default:
      for ($i = 0; $i < 2; $i++)
        $salt .= $chars[mt_rand(0, strlen($chars) - 1)];
      break;
  }

  return crypt($password, $salt);
}
