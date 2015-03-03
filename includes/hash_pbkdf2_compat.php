<?php
/**
 * https://github.com/rchouinard/hash_pbkdf2-compat
 * @author Ryan Chouinard
 */

require __DIR__ . '/hash_pbkdf2.php';

if (!function_exists('\hash_pbkdf2')) {
    function hash_pbkdf2($algo, $password, $salt, $iterations, $length = 0, $raw_output = false)
    {
        return \Rych\hash_pbkdf2($algo, $password, $salt, $iterations, $length, $raw_output);
    }
}

