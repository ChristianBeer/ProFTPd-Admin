<?php
/**
 * https://github.com/rchouinard/hash_pbkdf2-compat
 * @author Ryan Chouinard
 */

namespace Rych;

function hash_pbkdf2($algo, $password, $salt, $iterations, $length = 0, $raw_output = false)
{
    // Prep input arguments
    $algo       = (string)  isset($algo) ? $algo : null;
    $password   = (string)  isset($password) ? $password : null;
    $salt       = (string)  isset($salt) ? $salt : null;
    $iterations = (integer) isset($iterations) ? $iterations : null;
    $length     = (integer) $length;
    $raw_output = (boolean) $raw_output;

    // Recreate \hash_pbkdf2() error conditions
    $num_args = func_num_args();
    if ($num_args < 4) {
        trigger_error(sprintf('\%s() expects at least 4 parameters, %d given', __FUNCTION__, $num_args), E_USER_WARNING);
        return null;
    }

    if (!in_array($algo, hash_algos())) {
        trigger_error(sprintf('Unknown hashing algorithm: %s', $algo), E_USER_WARNING);
        return false;
    }

    if ($iterations <= 0) {
        trigger_error(sprintf('Iterations must be a positive integer: %d', $iterations), E_USER_WARNING);
        return false;
    }

    if ($length < 0) {
        trigger_error(sprintf('Length must be greater than or equal to 0: %d', $length), E_USER_WARNING);
        return false;
    }

    $salt_len = strlen($salt);
    if ($salt_len > PHP_INT_MAX - 4) {
        trigger_error(sprintf('Supplied salt is too long, max of PHP_INT_MAX - 4 bytes: %d supplied', $salt_len), E_USER_WARNING);
        return false;
    }

    // Algorithm implementation
    $hash_len = strlen(hash($algo, null, true));
    if ($length == 0) {
        $length = $hash_len;
    }

    $output = '';
    $block_count = ceil($length / $hash_len);
    for ($block = 1; $block <= $block_count; ++$block) {
        $key1 = $key2 = hash_hmac($algo, $salt . pack('N', $block), $password, true);
        for ($iteration = 1; $iteration < $iterations; ++$iteration) {
            $key2 ^= $key1 = hash_hmac($algo, $key1, $password, true);
        }
        $output .= $key2;
    }

    // Output the derived key
    // NOTE: The built-in \hash_pbkdf2() function trims the output to $length,
    // not the raw bytes before encoding as might be expected. I'm not a fan
    // of that decision, but it's emulated here for full compatibility.
    return substr(($raw_output) ? $output : bin2hex($output), 0, $length);
}

